<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Extended',
    'description' => 'Multi upload field, sender addresses in site config, new field types and other',
    'category' => 'misc',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'version' => '14.0.4',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.4.99',
            'form' => '14.0.0-14.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
