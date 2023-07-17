<?php
namespace WapplerSystems\FormExtended\Domain\Finishers;


use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Model\FormElements\FormElementInterface;
use WapplerSystems\FormExtended\Utility\UserUtility;

class FeUserFinisher extends \TYPO3\CMS\Form\Domain\Finishers\SaveToDatabaseFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = [
        'mode' => 'insert',
        'whereClause' => [],
        'elements' => [],
        'databaseColumnMappings' => [],
        'pid' => null,
        'dataProcessors' => []
    ];


    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $this->process(0);
    }

    /**
     * Perform the current database operation
     *
     * @param int $iterationCount
     */
    protected function process(int $iterationCount)
    {
        $this->throwExceptionOnInconsistentConfiguration();

        $table = 'fe_users';
        $elementsConfiguration = $this->parseOption('elements');
        $databaseColumnMappingsConfiguration = $this->parseOption('databaseColumnMappings');

        $this->databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);

        $databaseData = [];

        $databaseData['pid'] = $this->parseOption('pid');
        $databaseData['tstamp'] = time();

        foreach ($databaseColumnMappingsConfiguration as $databaseColumnName => $databaseColumnConfiguration) {
            $value = $this->parseOption('databaseColumnMappings.' . $databaseColumnName . '.value');
            if (
                empty($value)
                && ($databaseColumnConfiguration['skipIfValueIsEmpty'] ?? false) === true
            ) {
                continue;
            }

            $databaseData[$databaseColumnName] = $value;
        }

        $databaseData = $this->prepareData($elementsConfiguration, $databaseData);


        $this->saveToDatabase($databaseData, $table, $iterationCount);
    }


    /**
     * Prepare data for saving to database
     *
     * @param array $elementsConfiguration
     * @param array $databaseData
     * @return mixed
     */
    protected function prepareData(array $elementsConfiguration, array $databaseData)
    {
        foreach ($this->getFormValues() as $elementIdentifier => $elementValue) {
            if (
                ($elementValue === null || $elementValue === '')
                && isset($elementsConfiguration[$elementIdentifier])
                && isset($elementsConfiguration[$elementIdentifier]['skipIfValueIsEmpty'])
                && $elementsConfiguration[$elementIdentifier]['skipIfValueIsEmpty'] === true
            ) {
                continue;
            }
            if (
                ($elementValue === null || $elementValue === '')
                && isset($elementsConfiguration[$elementIdentifier])
                && isset($elementsConfiguration[$elementIdentifier]['valueIfValueIsEmpty'])
            ) {
                $elementValue = $elementsConfiguration[$elementIdentifier]['valueIfValueIsEmpty'];
            }

            $element = $this->getElementByIdentifier($elementIdentifier);
            if (
                !$element instanceof FormElementInterface
                || !isset($elementsConfiguration[$elementIdentifier])
                || !isset($elementsConfiguration[$elementIdentifier]['mapOnDatabaseColumn'])
            ) {
                continue;
            }

            if ($elementValue instanceof FileReference) {
                if (isset($elementsConfiguration[$elementIdentifier]['saveFileIdentifierInsteadOfUid'])) {
                    $saveFileIdentifierInsteadOfUid = (bool)$elementsConfiguration[$elementIdentifier]['saveFileIdentifierInsteadOfUid'];
                } else {
                    $saveFileIdentifierInsteadOfUid = false;
                }

                if ($saveFileIdentifierInsteadOfUid) {
                    $elementValue = $elementValue->getOriginalResource()->getCombinedIdentifier();
                } else {
                    $elementValue = $elementValue->getOriginalResource()->getProperty('uid_local');
                }
            } elseif (is_array($elementValue)) {
                $elementValue = implode(',', $elementValue);
            } elseif ($elementValue instanceof \DateTimeInterface) {
                $format = $elementsConfiguration[$elementIdentifier]['dateFormat'] ?? 'U';
                $elementValue = $elementValue->format($format);
            } elseif (isset($elementsConfiguration[$elementIdentifier]['hashPassword']) && $elementsConfiguration[$elementIdentifier]['hashPassword'] === true) {
                $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('FE');
                $elementValue = $hashInstance->getHashedPassword($elementValue);
            }

            $fields = explode(',',$elementsConfiguration[$elementIdentifier]['mapOnDatabaseColumn']);
            foreach ($fields as $field) {
                $databaseData[$field] = $elementValue;
            }
        }
        return $databaseData;
    }

    /**
     * Throws an exception if some inconsistent configuration
     * are detected.
     *
     * @throws FinisherException
     */
    protected function throwExceptionOnInconsistentConfiguration()
    {
        parent::throwExceptionOnInconsistentConfiguration();

        if (
            $this->options['pid'] === null
        ) {
            throw new FinisherException(
                'An empty option "pid" is not allowed.',
                1595979076
            );
        }
    }


}