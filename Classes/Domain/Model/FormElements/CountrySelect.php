<?php
declare(strict_types = 1);
namespace WapplerSystems\FormExtended\Domain\Model\FormElements;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Exception\IdentifierNotValidException;

/**
 * A generic form element
 *
 * Scope: frontend
 */
class CountrySelect extends \TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement
{

    /**
     * Constructor. Needs this FormElement's identifier and the FormElement type
     *
     * @param string $identifier The FormElement's identifier
     * @param string $type The Form Element Type
     * @throws IdentifierNotValidException
     */
    public function __construct(string $identifier, string $type)
    {
        parent::__construct($identifier, $type);

        if (class_exists(\SJBR\StaticInfoTables\Domain\Repository\CountryRepository::class)) {

            $countryRepository = GeneralUtility::makeInstance(\SJBR\StaticInfoTables\Domain\Repository\CountryRepository::class);

            $countries = $countryRepository->findAll();

            $this->setProperty('countries', $countries);
        } else {
            $this->setProperty('countries', []);
        }
    }



}
