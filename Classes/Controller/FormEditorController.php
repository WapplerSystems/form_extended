<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\Controller;

use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * The form editor controller
 *
 * Scope: backend
 * @internal
 */
class FormEditorController extends \TYPO3\CMS\Form\Controller\FormEditorController
{


    /**
     * @todo move this to FormDefinitionConversionService
     */
    protected function transformFormDefinitionForFormEditor(array $prototypeConfiguration, array $formDefinition): array
    {
        /** @var array<string, list<string>> $multiValueFormElementProperties */
        $multiValueFormElementProperties = [];
        /** @var array<string, list<string>> $multiValueFinisherProperties */
        $multiValueFinisherProperties = [];
        foreach ($prototypeConfiguration['formElementsDefinition'] as $type => $configuration) {
            if (!isset($configuration['formEditor']['editors'])) {
                continue;
            }
            foreach ($configuration['formEditor']['editors'] as $editorConfiguration) {
                if (($editorConfiguration['templateName'] ?? '') === 'Inspector-PropertyGridEditor') {
                    $multiValueFormElementProperties[$type][] = $editorConfiguration['propertyPath'];
                }
            }
        }
        foreach ($prototypeConfiguration['formElementsDefinition']['Form']['formEditor']['propertyCollections']['finishers'] ?? [] as $configuration) {
            if (!isset($configuration['editors'])) {
                continue;
            }
            foreach ($configuration['editors'] as $editorConfiguration) {
                if (($editorConfiguration['templateName'] ?? '') === 'Inspector-PropertyGridEditor') {
                    $multiValueFinisherProperties[$configuration['identifier']][] = $editorConfiguration['propertyPath'];
                }
            }
        }
        $formDefinition = $this->filterEmptyArrays($formDefinition);
        $formDefinition = $this->migrateEmailFinisherRecipients($formDefinition);
        // @todo: replace with rte parsing
        $formDefinition = ArrayUtility::stripTagsFromValuesRecursive($formDefinition);
        $formDefinition = $this->transformMultiValuePropertiesForFormEditor(
            $formDefinition,
            'type',
            $multiValueFormElementProperties
        );
        $formDefinition = $this->transformMultiValuePropertiesForFormEditor(
            $formDefinition,
            'identifier',
            $multiValueFinisherProperties
        );
        $formDefinition = $this->formDefinitionConversionService->addHmacData($formDefinition);
        return $formDefinition;
    }

}
