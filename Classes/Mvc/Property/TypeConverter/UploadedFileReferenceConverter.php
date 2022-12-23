<?php

namespace WapplerSystems\FormExtended\Mvc\Property\TypeConverter;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Form\Mvc\Property\Exception\TypeConverterException;
use TYPO3\CMS\Form\Slot\ResourcePublicationSlot;

class UploadedFileReferenceConverter extends \TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter {


    /**
     * Actually convert from $source to $targetType, taking into account the fully
     * built $convertedChildProperties and $configuration.
     *
     * @param array $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return AbstractFileFolder|AbstractFileFolder[]|Error|null
     * @internal
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {

        if (isset($source[0])) {
            $resources = [];
            foreach ($source as $singleSource) {
                $resources[] = $this->convertFromSourceToResource($singleSource, $targetType, $convertedChildProperties, $configuration);
            }
            return $resources;
        }
        return $this->convertFromSourceToResource($source, $targetType, $convertedChildProperties, $configuration);
    }

    protected function convertFromSourceToResource($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null) {

        // slot/listener using `FileDumpController` instead of direct public URL in (later) rendering process
        $resourcePublicationSlot = GeneralUtility::makeInstance(ResourcePublicationSlot::class);
        if (!isset($source['error']) || $source['error'] === \UPLOAD_ERR_NO_FILE) {
            if (isset($source['submittedFile']['resourcePointer'])) {
                try {
                    // File references use numeric resource pointers, direct
                    // file relations are using "file:" prefix (e.g. "file:5")
                    $resourcePointer = $this->hashService->validateAndStripHmac($source['submittedFile']['resourcePointer']);
                    if (strpos($resourcePointer, 'file:') === 0) {
                        $fileUid = (int)substr($resourcePointer, 5);
                        $resource = $this->createFileReferenceFromFalFileObject($this->resourceFactory->getFileObject($fileUid));
                    } else {
                        $resource = $this->createFileReferenceFromFalFileReferenceObject(
                            $this->resourceFactory->getFileReferenceObject($resourcePointer),
                            (int)$resourcePointer
                        );
                    }
                    $resourcePublicationSlot->add($resource->getOriginalResource()->getOriginalFile());
                    return $resource;
                } catch (\InvalidArgumentException $e) {
                    // Nothing to do. No file is uploaded and resource pointer is invalid. Discard!
                }
            }
            return null;
        }

        if ($source['error'] !== \UPLOAD_ERR_OK) {
            return GeneralUtility::makeInstance(Error::class, $this->getUploadErrorMessage($source['error']), 1471715915);
        }

        if (isset($this->convertedResources[$source['tmp_name']])) {
            return $this->convertedResources[$source['tmp_name']];
        }

        if ($configuration === null) {
            throw new \InvalidArgumentException('Argument $configuration must not be null', 1589183114);
        }

        try {
            $resource = $this->importUploadedResource($source, $configuration);
            $resourcePublicationSlot->add($resource->getOriginalResource()->getOriginalFile());
        } catch (TypeConverterException $e) {
            return $e->getError();
        } catch (\Exception $e) {
            return GeneralUtility::makeInstance(Error::class, $e->getMessage(), $e->getCode());
        }

        $this->convertedResources[$source['tmp_name']] = $resource;
        return $resource;

    }


}