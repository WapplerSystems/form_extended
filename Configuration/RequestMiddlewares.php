<?php

declare(strict_types=1);

use WapplerSystems\FormExtended\Middleware\PasswordPolicyEndpoint;

return [
    'frontend' => [
        'wapplersystems/form-extended/password-policy' => [
            'target' => PasswordPolicyEndpoint::class,
            // Sit right after SiteResolver so we still have access to the
            // resolved site (for language lookup), but BEFORE the redirect
            // handler and base-redirect-resolver — otherwise a request to
            // /_form_extended/password-policy on a site whose default
            // language has a non-empty base (e.g. /de/) gets 404'd by
            // page-resolver or rewritten by base-redirect before we ever
            // see it. Running this early also keeps the JSON URL out of
            // TYPO3's page-not-found lookup.
            'after' => ['typo3/cms-frontend/site'],
            'before' => [
                'typo3/cms-redirects/redirecthandler',
                'typo3/cms-frontend/base-redirect-resolver',
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
