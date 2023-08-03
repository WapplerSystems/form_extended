<?php

namespace WapplerSystems\FormExtended\Validation\Validator;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for not empty values.
 *
 * @api
 */
class FeUsernameAlreadyExistsValidator extends AbstractValidator
{

    /**
     * Checks if the given property ($propertyValue) is not empty (NULL, empty string, empty array or empty object).
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value): void
    {

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');
        $queryBuilder->getRestrictions()->removeAll();
        $count = $queryBuilder->count('uid')->from('fe_users')->where(
            $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($value))
        )->execute()->fetchOne();

        if ($count > 0) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.feUsernameAlreadyExists.true',
                    'form_extended'
                ),
                1591107223
            );
        }
    }
}
