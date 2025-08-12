<?php

declare(strict_types=1);


namespace WapplerSystems\FormExtended\EventListener;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\FormExtended\Event\AfterYamlConfigurationLoadedEvent;

/**
 *
 * Scope: backend
 * @internal
 */
class AfterYamlConfigurationLoadedEventListener
{

    public function __invoke(AfterYamlConfigurationLoadedEvent $event): void
    {
        $yamlConfiguration = $event->getYamlConfiguration();

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $options = [
            5 => [
                'value' => '',
                'label' => 'formEditor.elements.Form.finisher.Email.editor.language.option.frontend'
            ]
        ];
        $languages = [];

        /** @var Site $site */
        foreach ($siteFinder->getAllSites() as $site) {
            /** @var SiteLanguage $language */
            foreach ($site->getLanguages() as $language) {
                $languages[$language->getLocale()->getLanguageCode()] = $language->getTitle();
            }
        }
        foreach ($languages as $locale => $language) {
            $options[] = [
                'value' => $locale,
                'label' => $language
            ];
        }

        foreach ($yamlConfiguration['prototypes'] as $prototypeName => &$prototype) {
            foreach ($prototype['formElementsDefinition']['Form']['formEditor']['propertyCollections']['finishers'] as $finisherIndex => &$finisher) {
                foreach ($finisher['editors'] as $editorIndex => &$editor) {
                    if (($editor['identifier'] ?? '') === 'language') {
                        $editor['selectOptions'] = $options;
                    }
                }
            }
        }

        $event->setYamlConfiguration($yamlConfiguration);

    }
}
