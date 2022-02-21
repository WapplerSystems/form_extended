<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'WapplerSystems.FormExtended',
    'DoubleOptIn',
    [
        \WapplerSystems\FormExtended\Controller\DoubleOptInController::class => 'validation'
    ],
    [
        \WapplerSystems\FormExtended\Controller\DoubleOptInController::class => 'validation'
    ]
);

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);
$iconRegistry->registerIcon(
    'plugin-formextended',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:form_extended/Resources/Public/Icons/PluginDoubleOptIn.svg']
);


