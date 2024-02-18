<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WapplerSystems\FormExtended\Controller\DoubleOptInController;

defined('TYPO3_MODE') or die();

ExtensionUtility::configurePlugin(
    'WapplerSystems.FormExtended',
    'DoubleOptIn',
    [
        DoubleOptInController::class => 'validation'
    ],
    [
        DoubleOptInController::class => 'validation'
    ]
);

$iconRegistry = GeneralUtility::makeInstance(
    IconRegistry::class
);
$iconRegistry->registerIcon(
    'plugin-formextended',
    SvgIconProvider::class,
    ['source' => 'EXT:form_extended/Resources/Public/Icons/PluginDoubleOptIn.svg']
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Form\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::class] = [
    'className' => WapplerSystems\FormExtended\Mvc\Property\TypeConverter\UploadedFileReferenceConverter::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Form\Mvc\Property\PropertyMappingConfiguration::class] = [
    'className' => WapplerSystems\FormExtended\Mvc\Property\PropertyMappingConfiguration::class
];
