<?php
declare(strict_types=1);

namespace WapplerSystems\FormExtended\Domain\Finishers;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Exception;

/**
 * Scope: frontend
 */
class AttachUploadsToObjectFinisher extends AbstractFinisher
{
    /**
     * Executes this finisher
     * @throws Exception
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();

        $elementsOptions = $this->parseOption('elements');

        $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();
        foreach ($elements as $element) {
            if (!$element instanceof FileUpload) {
                continue;
            }
            $files = $formRuntime[$element->getIdentifier()];
            if (!$files) {
                continue;
            }
            if (!isset($elementsOptions[$element->getIdentifier()])) {
                continue;
            }
            $elementOptions = $elementsOptions[$element->getIdentifier()];

            if (!isset($elementOptions['table'])) {
                throw new Exception('no table defined in AttachUploadsToObjectFinisher for element ' . $element->getIdentifier());
            }

            $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($elementOptions['table']);

            if (($elementOptions['lastInsertId'] ?? false) === true) {
                $uid = $databaseConnection->lastInsertId();
            } else {
                $uid = $elementOptions['uid'] ?? null;
            }

            if ($uid === null) {
                throw new Exception('uid in AttachUploadsToObjectFinisher for element ' . $element->getIdentifier() . ' is null!');
            }

            $mapOnDatabaseColumn = $elementOptions['mapOnDatabaseColumn'] ?? null;
            if ($mapOnDatabaseColumn === null) {
                throw new Exception('mapOnDatabaseColumn in AttachUploadsToObjectFinisher for element ' . $element->getIdentifier() . ' is null!');
            }

            $contentElement = BackendUtility::getRecord(
                $elementOptions['table'],
                $uid
            );

            if (count(array_filter($files, function ($entry) {
                    return !($entry instanceof FileReference);
                })) === 0) {

                $fakeAdmin = GeneralUtility::makeInstance(BackendUserAuthentication::class);
                $fakeAdmin->start();
                $fakeAdmin->groupData['tables_modify'] = 'sys_file_reference,'.$elementOptions['table'];


                /** @var FileReference $file */
                foreach ($files as $file) {

                    $newId = 'NEW1234';
                    $data = [];
                    $data['sys_file_reference'][$newId] = [
                        'table_local' => 'sys_file',
                        'uid_local' => $file->getOriginalResource()->getProperty('uid_local'),
                        'tablenames' => $elementOptions['table'],
                        'uid_foreign' => $contentElement['uid'],
                        'fieldname' => $mapOnDatabaseColumn,
                        'pid' => $contentElement['pid'],
                    ];
                    $data[$elementOptions['table']][$contentElement['uid']] = [
                        $mapOnDatabaseColumn => $newId
                    ];

                    /** @var DataHandler $dataHandler */
                    $dataHandler = GeneralUtility::makeInstance(DataHandler::class);

                    $doktype = $dataHandler->pageInfo($contentElement['pid'], 'doktype');
                    if (!isset($GLOBALS['PAGES_TYPES'][$doktype]['allowedTables'])) {
                        $GLOBALS['PAGES_TYPES'][$doktype]['allowedTables'] = '*';
                    }
                    $dataHandler->bypassAccessCheckForRecords = true;
                    $dataHandler->bypassWorkspaceRestrictions = true;
                    $dataHandler->start($data, [], $fakeAdmin);
                    $dataHandler->process_datamap();
                    if (count($dataHandler->errorLog) > 0) {
                        throw new Exception(implode(',', $dataHandler->errorLog));
                    }

                }

            }

        }

    }

}
