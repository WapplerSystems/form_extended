<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\Form\FormDataProvider;

use TYPO3\CMS\Backend\Configuration\SiteTcaConfiguration;
use TYPO3\CMS\Core\Configuration\Processor\Placeholder\EnvPlaceholderProcessor;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Extended SiteDatabaseEditRow to support additional inline tables like 'site_sender'.
 */
readonly class SiteDatabaseEditRow extends \TYPO3\CMS\Backend\Form\FormDataProvider\SiteDatabaseEditRow
{
    public function __construct(
        SiteFinder $siteFinder,
        SiteTcaConfiguration $siteTcaConfiguration,
        private EnvPlaceholderProcessor $envPlaceholderProcessor,
    ) {
        parent::__construct($siteFinder, $siteTcaConfiguration, $envPlaceholderProcessor);
    }

    /**
     * First level of ['customData']['siteData'] to ['databaseRow']
     * Extended to support 'site_sender' inline table.
     *
     * @throws \RuntimeException
     */
    public function addData(array $result): array
    {
        if ($result['command'] !== 'edit' || !empty($result['databaseRow'])) {
            return $result;
        }

        $tableName = $result['tableName'];

        // Add 'site_sender' to supported inline tables
        if ($tableName === 'site_sender') {
            $unprocessedRootPageId = $result['inlineTopMostParentUid'] ?? $result['inlineParentUid'];

            $processedRootPageId = $this->envPlaceholderProcessor->canProcess($unprocessedRootPageId)
                ? (int)$this->envPlaceholderProcessor->process($unprocessedRootPageId)
                : (int)$unprocessedRootPageId;

            try {
                $rowData = $this->getRawConfigurationForSiteWithRootPageId($processedRootPageId);
                $parentFieldName = $result['inlineParentFieldName'];
                if (!isset($rowData[$parentFieldName])) {
                    throw new \RuntimeException('Field "' . $parentFieldName . '" not found', 1520886092);
                }
                $rowData = $rowData[$parentFieldName][$result['vanillaUid']];
                $result['databaseRow']['uid'] = $result['vanillaUid'];
            } catch (SiteNotFoundException $e) {
                $rowData = [];
            }

            foreach ($rowData as $fieldName => $value) {
                if (!is_array($value)) {
                    $result['databaseRow'][$fieldName] = $value;
                }
            }
            $result['databaseRow']['pid'] = 0;
            return $result;
        }

        // All other tables handled by parent
        return parent::addData($result);
    }

}
