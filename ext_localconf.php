<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::class] = [
    'className' => WapplerSystems\FormExtended\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Form\FormDataProvider\SiteTcaInline::class] = [
    'className' => WapplerSystems\FormExtended\Form\FormDataProvider\SiteTcaInline::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Backend\Form\FormDataProvider\SiteDatabaseEditRow::class] = [
    'className' => WapplerSystems\FormExtended\Form\FormDataProvider\SiteDatabaseEditRow::class
];


$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager::class] = [
    'className' => WapplerSystems\FormExtended\Mvc\Configuration\ConfigurationManager::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Form\Controller\FormEditorController::class] = [
    'className' => WapplerSystems\FormExtended\Controller\FormEditorController::class
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

// Register "formevh:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['formevh'][] = 'WapplerSystems\\FormExtended\\ViewHelpers';
