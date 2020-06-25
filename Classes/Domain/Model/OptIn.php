<?php

namespace WapplerSystems\FormExtended\Domain\Model;

/**
 * OptIn
 */
class OptIn extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $encodedValues = '';


    /**
     * isValidated
     *
     * @var bool
     */
    protected $isValidated = FALSE;

    /**
     * validationHash
     *
     * @var string
     */
    protected $validationHash = '';

    /**
     * validationDate
     *
     * @var \DateTime
     */
    protected $validationDate = null;

    public function __construct()
    {
        if (!$this->validationHash) {
            $this->validationHash = \WapplerSystems\FormExtended\Utility\Uuid::generate();
        }
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * Returns isValidated
     *
     * @return bool $isValidated
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Sets isValidated
     *
     * @param bool $isValidated
     * @return void
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;
    }

    /**
     * Returns the validationHash
     *
     * @return string $validationHash
     */
    public function getValidationHash()
    {
        return $this->validationHash;
    }

    /**
     * Sets the validationHash
     *
     * @param string $validationHash
     * @return void
     */
    public function setValidationHash($validationHash)
    {
        $this->validationHash = $validationHash;
    }

    /**
     * Returns the validationDate
     *
     * @return \DateTime $validationDate
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Sets the validationDate
     *
     * @param \DateTime $validationDate
     * @return void
     */
    public function setValidationDate(\DateTime $validationDate)
    {
        $this->validationDate = $validationDate;
    }


    /**
     * @return string
     */
    public function getEncodedValues(): string
    {
        return $this->encodedValues;
    }

    /**
     * @param string $encodedValues
     */
    public function setEncodedValues(string $encodedValues): void
    {
        $this->encodedValues = $encodedValues;
    }

    /**
     * @return array
     */
    public function getDecodedValues()
    {
        return json_decode($this->getEncodedValues());
    }

    public function setDecodedValues(array $values)
    {
        $this->setEncodedValues(json_encode($values));
    }


}
