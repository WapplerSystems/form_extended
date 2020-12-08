<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Extended',
    'description' => '',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'version' => '8.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7-8.7.99',
            'form' => '8.7-8.7.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
