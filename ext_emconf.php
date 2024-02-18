<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Extended',
    'description' => 'Multi upload field, double opt in method and other',
    'category' => 'misc',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'version' => '11.0.8',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-11.5.99',
            'form' => '11.0.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
