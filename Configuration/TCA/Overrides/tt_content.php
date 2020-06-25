<?php

/**
 * Disable not needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['formextended_doubleoptin'] = 'select_key,pages,recursive';

/**
 * Include Flexform
 */
// Pi1
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formextended_doubleoptin'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'formextended_doubleoptin',
    'FILE:EXT:form_extended/Configuration/FlexForms/DoubleOptIn.xml'
);
