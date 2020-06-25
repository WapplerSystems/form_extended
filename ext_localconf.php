<?php
defined('TYPO3_MODE') or die();


call_user_func(
    function ($extKey) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WapplerSystems.FormExtended',
            'DoubleOptIn',
            [
                'DoubleOptIn' => 'validation'
            ],
            [
                'DoubleOptIn' => 'validation'
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

    },
    'form_extended'
);
