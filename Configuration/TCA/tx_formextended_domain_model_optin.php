<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'email, given_name, family_name, company, customer_number, validation_hash',
        'iconfile' => 'EXT:form_extended/Resources/Public/Icons/PluginDoubleOptIn.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'email, encoded_values, validation_hash, validation_date, is_validated'
    ],
    'types' => [
        '1' => [
            'showitem' => 'email, encoded_values, validation_hash, validation_date, is_validated'
        ]
    ],
    'columns' => [
        'email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'encoded_values' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin.email',
            'config' => [
                'type' => 'text',
                'readOnly' => 1
            ]
        ],
        'is_validated' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin.is_validated',
            'config' => [
                'type' => 'check',
                'readOnly' => 1
            ]
        ],
        'validation_hash' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin.validation_hash',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'readOnly' => 1
            ]
        ],
        'validation_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_extended/Resources/Private/Language/locallang_db.xlf:tx_formextended_domain_model_optin.validation_date',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'readOnly' => 1
            ]
        ],
    ]
];
