<?php

declare(strict_types=1);


namespace WapplerSystems\FormExtended\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement;
use TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Translate form element properties.
 *
 * Scope: frontend / backend
 */
final class TranslateElementPropertyViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('element', RootRenderableInterface::class, 'Form Element to translate', true);
        $this->registerArgument('property', 'mixed', 'Property to translate');
        $this->registerArgument('renderingOptionProperty', 'mixed', 'Property to translate');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
    }

    /**
     * Return array element by key.
     *
     * @return string|array
     */
    public function render()
    {
        self::assertArgumentTypes($this->arguments);
        /** @var GenericFormElement $element */
        $element = $this->arguments['element'];
        $translateArguments = $this->arguments['arguments'];

        $property = null;
        if (!empty($this->arguments['property'])) {
            $property = $this->arguments['property'];
        } elseif (!empty($this->arguments['renderingOptionProperty'])) {
            $property = $this->arguments['renderingOptionProperty'];
        }
        if (is_string($property) && is_array($translateArguments)) {
            $element->setRenderingOption('translation', ['arguments' => [ $property => $translateArguments ] ]);
        }

        if (empty($property)) {
            $propertyParts = [];
        } elseif (is_array($property)) {
            $propertyParts = $property;
        } else {
            $propertyParts = [$property];
        }
        /** @var FormRuntime $formRuntime */
        $formRuntime = $this->renderingContext
            ->getViewHelperVariableContainer()
            ->get(RenderRenderableViewHelper::class, 'formRuntime');
        return GeneralUtility::makeInstance(TranslationService::class)->translateFormElementValue($element, $propertyParts, $formRuntime);
    }

    protected static function assertArgumentTypes(array $arguments)
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
}
