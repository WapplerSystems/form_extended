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
    'version' => '11.0.5',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-11.5.99',
            'form' => '11.0.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
