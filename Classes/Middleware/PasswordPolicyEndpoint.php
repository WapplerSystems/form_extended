<?php
declare(strict_types=1);

namespace WapplerSystems\FormExtended\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\PasswordPolicy\Validator\CorePasswordValidator;

/**
 * Lightweight JSON endpoint that exposes the frontend password policy
 * configuration so client-side JavaScript can mirror the same rules in
 * a live "is your password strong enough yet?" indicator under the
 * form's password input.
 *
 * Path: /_form_extended/password-policy
 *
 * Response: {
 *   "policy": "default",
 *   "rules": [
 *     {"id": "minimumLength",            "label": "…", "value": 8},
 *     {"id": "upperCaseCharacterRequired","label": "…"},
 *     {"id": "lowerCaseCharacterRequired","label": "…"},
 *     {"id": "digitCharacterRequired",    "label": "…"},
 *     {"id": "specialCharacterRequired",  "label": "…"}
 *   ]
 * }
 *
 * Only the rules that the configured CorePasswordValidator actually
 * enforces are returned; an FE policy that disables specialCharacter
 * will simply not have a "specialCharacterRequired" rule in the
 * response, so the indicator stays in sync with the validator.
 */
final class PasswordPolicyEndpoint implements MiddlewareInterface
{
    private const PATH = '/_form_extended/password-policy';

    public function __construct(
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Match path by suffix so the endpoint also fires after
        // SiteBaseRedirectResolver has prepended a language base (e.g.
        // /de/_form_extended/password-policy). Without this, sites whose
        // default language has a non-empty base would 307-redirect the
        // request out of our reach on the first pass and the
        // language-prefixed second pass would no longer match an exact
        // string comparison.
        if (!str_ends_with($request->getUri()->getPath(), self::PATH)) {
            return $handler->handle($request);
        }

        $policyName = (string)($GLOBALS['TYPO3_CONF_VARS']['FE']['passwordPolicy'] ?? 'default');
        $policies = $GLOBALS['TYPO3_CONF_VARS']['SYS']['passwordPolicies'] ?? [];
        $validators = $policies[$policyName]['validators'] ?? [];
        $coreOptions = $validators[CorePasswordValidator::class]['options'] ?? null;

        // Empty policy or no CorePasswordValidator → no client-side rules
        // to render. Return an empty rule list so the JS knows nothing
        // is required (vs. failing the fetch).
        if (!is_array($coreOptions)) {
            return new JsonResponse(['policy' => $policyName, 'rules' => []]);
        }

        $site = $request->getAttribute('site');

        // The endpoint URL itself has no language prefix, so the client
        // hands us the active page language via `?lang=…`. Match it
        // against the site's configured languages by ISO code OR hreflang
        // (so both "en" and "en-US" resolve cleanly). Fall back to the
        // site default if the param is missing or unknown.
        $langParam = trim((string)($request->getQueryParams()['lang'] ?? ''));
        $language = null;
        if ($langParam !== '' && $site !== null) {
            foreach ($site->getLanguages() as $candidate) {
                $locale = $candidate->getLocale();
                $languageCode = $locale->getLanguageCode();
                if (strcasecmp($languageCode, $langParam) === 0
                    || strcasecmp($candidate->getHreflang(), $langParam) === 0
                    || strcasecmp((string)$locale, $langParam) === 0
                ) {
                    $language = $candidate;
                    break;
                }
            }
        }
        if ($language === null) {
            $language = $site !== null && method_exists($site, 'getDefaultLanguage')
                ? $site->getDefaultLanguage()
                : null;
        }
        $ls = $language !== null
            ? $this->languageServiceFactory->createFromSiteLanguage($language)
            : $this->languageServiceFactory->create('default');

        $labelOf = static fn (string $key): string => $ls->sL(
            'LLL:EXT:core/Resources/Private/Language/locallang_password_policy.xlf:requirement.' . $key,
        ) ?: $ls->sL(
            'LLL:EXT:core/Resources/Private/Language/locallang_password_policy.xlf:error.' . $key,
        ) ?: $key;

        $rules = [];
        $minLength = (int)($coreOptions['minimumLength'] ?? 0);
        if ($minLength > 0) {
            $rules[] = [
                'id' => 'minimumLength',
                'value' => $minLength,
                'label' => sprintf($labelOf('minimumLength') ?: 'At least %d characters', $minLength),
            ];
        }
        foreach ([
            'upperCaseCharacterRequired',
            'lowerCaseCharacterRequired',
            'digitCharacterRequired',
            'specialCharacterRequired',
        ] as $flag) {
            if (!empty($coreOptions[$flag])) {
                $rules[] = ['id' => $flag, 'label' => $labelOf($flag)];
            }
        }

        return new JsonResponse([
            'policy' => $policyName,
            'rules' => $rules,
        ]);
    }
}
