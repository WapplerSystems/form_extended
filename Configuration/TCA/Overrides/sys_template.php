<?php

/***************
* Add content element PageTSConfig
*/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::registerPageTSConfigFile(
'form_extended',
'Configuration/TsConfig/NewContentElement.tsconfig',
'Form Double Opt-In'
);

ExtensionManagementUtility::addStaticFile('form_extended', 'Configuration/TypoScript/', 'Form Extended');
