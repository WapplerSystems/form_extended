<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\Form\FormDataProvider;

/**
 * Extended SiteTcaInline to support additional inline tables like 'site_sender'.
 */
class SiteTcaInline extends \TYPO3\CMS\Backend\Form\FormDataProvider\SiteTcaInline
{
    /**
     * Resolve inline fields.
     * Extended to support 'site_sender' inline table.
     */
    public function addData(array $result): array
    {
        $result = $this->addInlineFirstPid($result);
        foreach ($result['processedTca']['columns'] as $fieldName => $fieldConfig) {
            if (!$this->isInlineField($fieldConfig)) {
                continue;
            }
            $childTableName = $fieldConfig['config']['foreign_table'] ?? '';
            if (!in_array($childTableName, ['site_errorhandling', 'site_route', 'site_base_variant', 'site_sender'], true)) {
                throw new \RuntimeException('Inline relation to other tables not implemented', 1522494737);
            }
            $result['processedTca']['columns'][$fieldName]['children'] = [];
            $result = $this->resolveSiteRelatedChildren($result, $fieldName);
            if (!empty($result['processedTca']['columns'][$fieldName]['config']['selectorOrUniqueConfiguration'])) {
                throw new \RuntimeException('selectorOrUniqueConfiguration not implemented in sites module', 1624313533);
            }
        }

        return $result;
    }

}
