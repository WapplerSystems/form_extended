<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


$iconRegistry = GeneralUtility::makeInstance(
    IconRegistry::class
);
$iconRegistry->registerIcon(
    'plugin-formextended',
    SvgIconProvider::class,
    ['source' => 'EXT:form_extended/Resources/Public/Icons/PluginDoubleOptIn.svg']
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Form\FormDataProvider\SiteTcaInline::class] = [
    'className' => WapplerSystems\FormExtended\Form\FormDataProvider\SiteTcaInline::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Form\FormDataProvider\SiteDatabaseEditRow::class] = [
    'className' => WapplerSystems\FormExtended\Form\FormDataProvider\SiteDatabaseEditRow::class
];


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

