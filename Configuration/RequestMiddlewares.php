<?php

declare(strict_types=1);

use WapplerSystems\FormExtended\Middleware\PasswordPolicyEndpoint;

return [
    'frontend' => [
        'wapplersystems/form-extended/password-policy' => [
            'target' => PasswordPolicyEndpoint::class,
            // The policy is the same for every site, so it doesn't matter
            // strictly where we run; placing this before page resolution
            // keeps the JSON URL out of TYPO3's page-not-found lookup.
            'after' => ['typo3/cms-frontend/site'],
            'before' => ['typo3/cms-frontend/page-resolver'],
        ],
    ],
];
