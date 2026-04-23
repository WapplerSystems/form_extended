<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


ExtensionManagementUtility::addTypoScriptSetup(
    'module.tx_form {
    settings {
        yamlConfigurations {
            321 = EXT:form_extended/Configuration/Yaml/FormSetup.yaml
        }
    }
}'
);

$featureSiteEmail = GeneralUtility::makeInstance(ExtensionConfiguration::class)
    ->get('form_extended', 'featureSiteEmail');
if ($featureSiteEmail) {
    ExtensionManagementUtility::addTypoScriptSetup(
        'module.tx_form {
            settings {
                yamlConfigurations {
                    322 = EXT:form_extended/Configuration/Yaml/Features/SiteEmail/Feature.yaml
                }
            }
        }'
    );
}

// Register "formevh:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['formevh'][] = 'WapplerSystems\\FormExtended\\ViewHelpers';
