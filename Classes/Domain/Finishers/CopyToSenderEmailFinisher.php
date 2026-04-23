<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\Domain\Finishers;

use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;

/**
 * Sends a copy of the form submission email to the sender.
 */
class CopyToSenderEmailFinisher extends EmailFinisher
{

    /**
     * Returns whether this finisher is enabled
     *
     * @return bool
     * @throws FinisherException
     */
    public function isEnabled(): bool
    {
        $conditionFieldName = $this->parseOption('conditionFieldName');
        if ($conditionFieldName === null) {
            throw new FinisherException('The option "conditionFieldName" must be set for the CopyToSenderEmailFinisher.', 1612660449);
        }
        if ((bool)($conditionFieldName) === false) {
            return false;
        }
        return !isset($this->options['renderingOptions']['enabled']) || (bool)$this->parseOption('renderingOptions.enabled') === true;
    }


}
