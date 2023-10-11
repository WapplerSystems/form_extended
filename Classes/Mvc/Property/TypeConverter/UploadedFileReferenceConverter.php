<?php

namespace WapplerSystems\FormExtended\Mvc\Property\TypeConverter;


use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Form\Mvc\Property\Exception\TypeConverterException;
use TYPO3\CMS\Form\Mvc\Property\TypeConverter\PseudoFile;
use TYPO3\CMS\Form\Mvc\Property\TypeConverter\PseudoFileReference;
use TYPO3\CMS\Form\Mvc\Validation\FileSizeValidator;
use TYPO3\CMS\Form\Mvc\Validation\MimeTypeValidator;
use TYPO3\CMS\Form\Slot\ResourcePublicationSlot;
use WapplerSystems\FormExtended\Mvc\Validation\FileCollectionSizeValidator;
use WapplerSystems\FormExtended\Mvc\Validation\FileCountValidator;

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
        if (is_array($source) && !isset($source['tmp_name'])) {
            $resources = [];

            foreach ($source as $singleSource) {
                if (isset($singleSource['tmp_name']) && $singleSource['tmp_name'] === '') {
                    continue;
                }
                if (isset($singleSource['resourcePointer'])) {
                    $resources = array_merge($resources, $this->convertFromPointerToResource($singleSource, $targetType, $convertedChildProperties, $configuration));
                } else {
                    $resources[] = $this->convertFromSourceToResource($singleSource, $targetType, $convertedChildProperties, $configuration);
                }
            }

            // validate
            if ($configuration !== null) {
                $validators = $configuration->getConfigurationValue(\TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::class,\TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::CONFIGURATION_FILE_VALIDATORS);

                foreach ($validators as $validator) {
                    if ($validator instanceof FileCollectionSizeValidator || $validator instanceof FileCountValidator) {
                        $validationResult = $validator->validate($resources);
                        if ($validationResult->hasErrors()) {
                            return $validationResult->getErrors()[0];
                        }
                    }
                }

            }

            return $resources;
        }
        // single file
        return $this->convertFromSourceToResource($source, $targetType, $convertedChildProperties, $configuration);

    }

    protected function convertFromPointerToResource($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null): array {
        // slot/listener using `FileDumpController` instead of direct public URL in (later) rendering process
        $resourcePublicationSlot = GeneralUtility::makeInstance(ResourcePublicationSlot::class);

        if (!isset($source['error']) || $source['error'] === \UPLOAD_ERR_NO_FILE) {

            if (is_array($source['resourcePointer'] ?? null)) {
                $resources = [];
                foreach ($source['resourcePointer'] as $resourcePointerSource) {
                    try {
                        // File references use numeric resource pointers, direct
                        // file relations are using "file:" prefix (e.g. "file:5")
                        $resourcePointer = $this->hashService->validateAndStripHmac($resourcePointerSource);
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
                        $resources[] = $resource;
                    } catch (\InvalidArgumentException $e) {
                        // Nothing to do. No file is uploaded and resource pointer is invalid. Discard!
                    }
                }
                return $resources;
            }
        }
        return [];
    }

    protected function convertFromSourceToResource($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null): Error|PseudoFileReference|null {
        // slot/listener using `FileDumpController` instead of direct public URL in (later) rendering process
        $resourcePublicationSlot = GeneralUtility::makeInstance(ResourcePublicationSlot::class);


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


    /**
     * Import a resource and respect configuration given for properties
     *
     * @param array $uploadInfo
     * @param PropertyMappingConfigurationInterface $configuration
     * @return PseudoFileReference
     */
    protected function importUploadedResource(
        array $uploadInfo,
        PropertyMappingConfigurationInterface $configuration
    ): PseudoFileReference {
        if (!GeneralUtility::makeInstance(FileNameValidator::class)->isValid($uploadInfo['name'])) {
            throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1471710357);
        }
        // `CONFIGURATION_UPLOAD_SEED` is expected to be defined
        // if it's not given any random seed is generated, instead of throwing an exception
        $seed = $configuration->getConfigurationValue(self::class, self::CONFIGURATION_UPLOAD_SEED)
            ?: GeneralUtility::makeInstance(Random::class)->generateRandomHexString(40);
        $uploadFolderId = $configuration->getConfigurationValue(self::class, self::CONFIGURATION_UPLOAD_FOLDER) ?: $this->defaultUploadFolder;
        $conflictMode = $configuration->getConfigurationValue(self::class, self::CONFIGURATION_UPLOAD_CONFLICT_MODE) ?: $this->defaultConflictMode;
        $pseudoFile = GeneralUtility::makeInstance(PseudoFile::class, $uploadInfo);

        $validators = $configuration->getConfigurationValue(self::class, self::CONFIGURATION_FILE_VALIDATORS);
        if (is_array($validators)) {
            foreach ($validators as $validator) {
                if ($validator instanceof FileSizeValidator || $validator instanceof MimeTypeValidator) {
                    $validationResult = $validator->validate($pseudoFile);
                    if ($validationResult->hasErrors()) {
                        throw TypeConverterException::fromError($validationResult->getErrors()[0]);
                    }
                }
            }
        }

        $uploadFolder = $this->provideUploadFolder($uploadFolderId);
        // current folder name, derived from public random seed (`formSession`)
        $currentName = 'form_' . GeneralUtility::hmac($seed, self::class);
        $uploadFolder = $this->provideTargetFolder($uploadFolder, $currentName);
        // sub-folder in $uploadFolder with 160 bit of derived entropy (.../form_<40-chars-hash>/actual.file)
        $uploadedFile = $uploadFolder->addUploadedFile($uploadInfo, $conflictMode);

        $resourcePointer = isset($uploadInfo['submittedFile']['resourcePointer']) && !str_contains($uploadInfo['submittedFile']['resourcePointer'], 'file:')
            ? (int)$this->hashService->validateAndStripHmac($uploadInfo['submittedFile']['resourcePointer'])
            : null;

        return $this->createFileReferenceFromFalFileObject($uploadedFile, $resourcePointer);
    }


}
