<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace WapplerSystems\FormExtended\ViewHelpers\Form;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper;

/**
 * This ViewHelper makes the specified Image object available for its
 * childNodes.
 * In case the form is redisplayed because of validation errors, a previously
 * uploaded image will be correctly used.
 *
 * Scope: frontend
 */
class UploadedResourceViewHelper extends UploadViewHelper
{
    /**
     * @var HashService
     */
    protected $hashService;

    /**
     * @var \TYPO3\CMS\Extbase\Property\PropertyMapper
     */
    protected $propertyMapper;

    /**
     * @param HashService $hashService
     * @internal
     */
    public function injectHashService(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Property\PropertyMapper $propertyMapper
     * @internal
     */
    public function injectPropertyMapper(PropertyMapper $propertyMapper)
    {
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * Initialize the arguments.
     *
     * @internal
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('as', 'string', '');
        $this->registerArgument('accept', 'array', 'Values for the accept attribute', false, []);
    }

    /**
     * @return string
     */
    public function render()
    {
        $output = '';

        $as = $this->arguments['as'];
        $accept = $this->arguments['accept'];
        $resources = $this->getUploadedResources();

        if (!empty($accept)) {
            $this->tag->addAttribute('accept', implode(',', $accept));
        }
        $output .= parent::render();

        if ($resources !== null) {
            foreach ($resources as $key => $resource) {
                $resourcePointerIdAttribute = '';
                if ($this->hasArgument('id')) {
                    $resourcePointerIdAttribute = ' id="' . htmlspecialchars($this->arguments['id']) . '-file-reference-'.$key.'"';
                }
                $resourcePointerValue = $resource->getUid();
                if ($resourcePointerValue === null) {
                    // Newly created file reference which is not persisted yet.
                    // Use the file UID instead, but prefix it with "file:" to communicate this to the type converter
                    $resourcePointerValue = 'file:' . $resource->getOriginalResource()->getOriginalFile()->getUid();
                }
                $output .= '<input type="hidden" name="' . htmlspecialchars($this->getName()) . '[submittedFile][resourcePointer][]" value="' . htmlspecialchars($this->hashService->appendHmac($resourcePointerValue)) . '"' . $resourcePointerIdAttribute . ' />';
            }
            $this->templateVariableContainer->add($as, $resources);
            $output .= $this->renderChildren();
            $this->templateVariableContainer->remove($as);
        } else {
            $output .= $this->renderChildren();
        }

        return $output;
    }

    /**
     * Return a previously uploaded resource.
     * Return NULL if errors occurred during property mapping for this property.
     *
     * @return array
     */
    protected function getUploadedResources()
    {
        if ($this->getMappingResultsForProperty()->hasErrors()) {
            return null;
        }
        $return = [];
        $resources = $this->getValueAttribute();
        if (is_array($resources)) {
            foreach ($resources as $resource) {
                if ($resource === null) continue;
                if ($resource instanceof FileReference) {
                    $return[] = $resource;
                    continue;
                }
                $ex = $this->propertyMapper->convert($resource, FileReference::class);
                $return = array_merge($return,$ex);
            }
        }
        return $return;
    }

    protected function getValueAttribute()
    {
        $value = null;

        if ($this->respectSubmittedDataValue) {
            $value = $this->getValueFromSubmittedFormData($value);
        } elseif ($this->hasArgument('value')) {
            $value = $this->arguments['value'];
        } elseif ($this->isObjectAccessorMode()) {
            $value = $this->getPropertyValue();
        }

        $value = $this->convertToPlainValue($value);
        return $value;
    }
}
