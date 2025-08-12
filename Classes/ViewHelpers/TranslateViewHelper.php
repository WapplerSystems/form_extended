<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\ViewHelpers;


use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface as ExtbaseRequestInterface;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

final class TranslateViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('key', 'string', 'Translation Key');
        $this->registerArgument('id', 'string', 'Translation ID. Same as key.');
        $this->registerArgument('default', 'string', 'If the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
        $this->registerArgument('extensionName', 'string', 'UpperCamelCased extension key (for example BlogExample)');
    }

    /**
     * Return array element by key.
     */
    public function render(): array|string
    {
        $key = $this->arguments['key'];
        $id = $this->arguments['id'];
        $default = (string)($this->arguments['default'] ?? $this->renderChildren() ?? '');
        $extensionName = $this->arguments['extensionName'];
        $translateArguments = $this->arguments['arguments'];
        // Use key if id is empty.
        if ($id === null) {
            $id = $key;
        }
        $id = (string)$id;
        if ($id === '') {
            throw new Exception('An argument "key" or "id" has to be provided', 1351584844);
        }
        $request = null;
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        if (empty($extensionName)) {
            if ($request instanceof ExtbaseRequestInterface) {
                $extensionName = $request->getControllerExtensionName();
            } elseif (str_starts_with($id, 'LLL:EXT:')) {
                $extensionName = substr($id, 8, strpos($id, '/', 8) - 8);
            } elseif ($default) {
                return self::handleDefaultValue($default, $translateArguments);
            } else {
                // Throw exception in case neither an extension key nor a extbase request
                // are given, since the "short key" shouldn't be considered as a label.
                throw new \RuntimeException(
                    'ViewHelper f:translate in non-extbase context needs attribute "extensionName" to resolve'
                    . ' key="' . $id . '" without path. Either set attribute "extensionName" together with the short'
                    . ' key "yourKey" to result in a lookup "LLL:EXT:your_extension/Resources/Private/Language/locallang.xlf:yourKey",'
                    . ' or (better) use a full LLL reference like key="LLL:EXT:your_extension/Resources/Private/Language/yourFile.xlf:yourKey".'
                    . ' Alternatively, you can also define a default value.',
                    1639828178
                );
            }
        }

        return GeneralUtility::makeInstance(TranslationService::class)->translate($id, $translateArguments);
    }

    protected static function assertArgumentTypes(array $arguments): void
    {
        foreach (['property', 'renderingOptionProperty'] as $argumentName) {
            if (
                !isset($arguments[$argumentName])
                || is_string($arguments[$argumentName])
                || is_array($arguments[$argumentName])
            ) {
                continue;
            }
            throw new Exception(
                sprintf(
                    'Arguments "%s" either must be string or array',
                    $argumentName
                ),
                1504871830
            );
        }
    }

    protected static function handleDefaultValue(string $default, ?array $translateArguments): string
    {
        if (!empty($translateArguments)) {
            return vsprintf($default, $translateArguments);
        }
        return $default;
    }
}
