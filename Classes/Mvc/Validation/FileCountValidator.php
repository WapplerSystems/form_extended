<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/*
 * Inspired by and partially taken from the Neos.Form package (www.neos.io)
 */

namespace WapplerSystems\FormExtended\Mvc\Validation;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for countable types
 *
 * Scope: frontend
 * @internal
 */
class FileCountValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'minimum' => [0, 'The minimum count of files to accept', 'integer'],
        'maximum' => [PHP_INT_MAX, 'The maximum count of files to accept', 'integer'],
    ];

    /**
     * The given value is valid if it is an array or \Countable that contains the specified amount of elements.
     *
     * @param array $value
     */
    public function isValid($value)
    {

        if (!is_array($value) && !($value instanceof \Countable)) {
            $this->addError(
                $this->translateErrorMessage(
                    'validation.error.1697065882',
                    'form_extended'
                ),
                1697065882
            );
            return;
        }

        $minimum = (int)($this->options['minimum'] ?? 0);
        $maximum = (int)($this->options['maximum'] ?? 0);
        if (count($value) < $minimum || count($value) > $maximum) {
            $this->addError(
                $this->translateErrorMessage(
                    'validation.error.1697065997',
                    'form_extended',
                    [$minimum, $maximum]
                ),
                1697065997,
                [$this->options['minimum'], $this->options['maximum']]
            );
        }
    }


}
