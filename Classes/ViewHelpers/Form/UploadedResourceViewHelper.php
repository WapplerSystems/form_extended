<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\ViewHelpers\Form;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * This ViewHelper makes the specified Image object available for its
 * childNodes.
 * In case the form is redisplayed because of validation errors, a previously
 * uploaded image will be correctly used.
 *
 * Scope: frontend
 */
class UploadedResourceViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'input';

    protected HashService $hashService;
    protected PropertyMapper $propertyMapper;

    public function injectHashService(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    public function injectPropertyMapper(PropertyMapper $propertyMapper)
    {
        $this->propertyMapper = $propertyMapper;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('as', 'string', '');
        $this->registerArgument('accept', 'array', 'Values for the accept attribute', false, []);
        $this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this ViewHelper', false, 'f3-form-error');
        $this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
        $this->registerTagAttribute('multiple', 'string', 'Specifies that the file input element should allow multiple selection of files');
        $this->registerUniversalTagAttributes();
    }

    public function render(): string
    {
        $output = '';

        $name = $this->getName();
        $as = $this->arguments['as'];
        $accept = $this->arguments['accept'];
        $resource = $this->getUploadedResource();

        if (!empty($accept)) {
            $this->tag->addAttribute('accept', implode(',', $accept));
        }

        if ($resource !== null) {
            $resourcePointerIdAttribute = '';
            if ($this->hasArgument('id')) {
                $resourcePointerIdAttribute = ' id="' . htmlspecialchars($this->arguments['id']) . '-file-reference"';
            }

            if(is_array($resource)) {
                foreach ($resource as $item) {
                   $this->addFileInFormFrontend($item, $resourcePointerIdAttribute, $as, $output);
                }
            } else {
                $this->addFileInFormFrontend($resource, $resourcePointerIdAttribute, $as, $output);
            }
        }

        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $fieldName) {
            $this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . ']');
        }
        $this->tag->addAttribute('type', 'file');

        if (isset($this->arguments['multiple'])) {
            $this->tag->addAttribute('name', $name . '[]');
        } else {
            $this->tag->addAttribute('name', $name);
        }

        $this->setErrorClassAttribute();
        $output .= $this->tag->render();

        return $output;
    }

    /**
     * Return a previously uploaded resource.
     * Return NULL if errors occurred during property mapping for this property.
     */
    protected function getUploadedResource()
    {
        if ($this->getMappingResultsForProperty()->hasErrors()) {
            return null;
        }
        $resource = $this->getValueAttribute();



        if (is_array($resource)) {
            $collector = [];

            foreach ($resource as $item) {
                $item instanceof FileReference
                    ? $collector[] = $item
                    : $this->propertyMapper->convert($item, FileReference::class);
            }
            return $collector;
        }
        if ($resource instanceof FileReference) {
            return $resource;
        }

        return $this->propertyMapper->convert($resource, FileReference::class);
    }

    protected function addFileInFormFrontend($resource, $resourcePointerIdAttribute, $as, &$output) {
        $resourcePointerValue = $resource->getUid();
        if ($resourcePointerValue === null) {
            // Newly created file reference which is not persisted yet.
            // Use the file UID instead, but prefix it with "file:" to communicate this to the type converter
            $resourcePointerValue = 'file:' . $resource->getOriginalResource()->getOriginalFile()->getUid();
        }

        $output .= '<input type="hidden" name="' . htmlspecialchars($this->getName()) . '[submittedFile][resourcePointer]" value="' . htmlspecialchars($this->hashService->appendHmac((string)$resourcePointerValue)) . '"' . $resourcePointerIdAttribute . ' />';
        $this->templateVariableContainer->add($as, $resource);
        $output .= $this->renderChildren();
        $this->templateVariableContainer->remove($as);
    }
}
