<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'WapplerSystems.FormExtended',
            'DoubleOptIn',
            'DoubleOptIn Validation'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript/DoubleOptIn', 'Form Extended - Double Opt-In');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_formextended_domain_model_optin');
    },
    'form_extended'
);
