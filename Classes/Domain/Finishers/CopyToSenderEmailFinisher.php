<?php

namespace WapplerSystems\FormExtended\Domain\Finishers;

use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;

/**
 * Class CopyToSenderEmailFinisher
 *
 *
 * @package WapplerSystems\FormExtended\Domain\Finishers
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
        if ((boolean)($conditionFieldName) === false) {
            return false;
        }
        return !isset($this->options['renderingOptions']['enabled']) || (bool)$this->parseOption('renderingOptions.enabled') === true;
    }


}
