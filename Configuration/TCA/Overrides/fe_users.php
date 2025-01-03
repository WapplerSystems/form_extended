<?php
declare(strict_types=1);


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


$newSysFileReferenceColumns = [
    'registration_completed' => [
        'exclude' => true,
        'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:registration_completed',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
            'items' => [
                [
                    0 => '',
                    1 => '',
                ]
            ],
            'readOnly' => true
        ]
    ],
];

ExtensionManagementUtility::addTCAcolumns('fe_users', $newSysFileReferenceColumns);

ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'registration_completed', '', '');

