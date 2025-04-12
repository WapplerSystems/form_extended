<?php

declare(strict_types=1);


namespace WapplerSystems\FormExtended\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModifyFlexFormListener
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $featureSiteEmail = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('form_extended', 'featureSiteEmail');

        if (!$featureSiteEmail) {
            return;
        }

        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();
        if (($identifier['ext-form-overrideFinishers'] ?? '') === '') {
            return;
        }

        $fieldPath = ['settings.finishers.EmailToReceiver.senderAddress','settings.finishers.EmailToReceiver.senderName'];

        foreach ($dataStructure['sheets'] as $sheetName => $sheet) {
            if (isset($dataStructure['sheets'][$sheetName]['ROOT']['el'])) {
                foreach ($dataStructure['sheets'][$sheetName]['ROOT']['el'] as $key => $fieldConfig) {
                    if (in_array($key, $fieldPath, true) && is_array($fieldConfig)) {
                        $fieldConfig['config']['type'] = 'hidden';
                        $dataStructure['sheets'][$sheetName]['ROOT']['el'][$key] = $fieldConfig;
                    }
                }
            }
        }

        $event->setDataStructure($dataStructure);
    }
}
