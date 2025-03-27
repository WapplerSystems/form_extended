<?php

declare(strict_types=1);


namespace WapplerSystems\FormExtended\Domain\Model\FormElements;

use TYPO3\CMS\Form\Domain\Model\FormElements\AbstractFormElement;
use TYPO3\CMS\Form\Domain\Model\FormElements\StringableFormElementInterface;

/**
 * A date form element
 *
 * Scope: frontend
 */
class Time extends AbstractFormElement implements StringableFormElementInterface
{
    /**
     * Initializes the Form Element by setting the data type to "DateTime"
     * @internal
     */
    public function initializeFormElement()
    {
        $this->setDataType(\DateTime::class);
        parent::initializeFormElement();
    }

    /**
     * @param \DateTime $value
     */
    public function valueToString($value): string
    {
        $dateFormat = $this->properties['displayFormat'] ?? 'H:s';

        return $value->format($dateFormat);
    }
}
