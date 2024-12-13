<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(
    function($extKey)
    {
        ExtensionUtility::registerPlugin(
            'form_extended',
            'DoubleOptIn',
            'DoubleOptIn Validation'
        );

        ExtensionManagementUtility::addStaticFile('form_extended', 'Configuration/TypoScript/DoubleOptIn', 'Form Extended - Double Opt-In');
    },
    'form_extended'
);
