<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace WapplerSystems\FormExtended\Mvc\Configuration;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Mvc\Configuration\TypoScriptService;
use TYPO3\CMS\Form\Mvc\Configuration\YamlSource;
use WapplerSystems\FormExtended\Event\AfterYamlConfigurationLoadedEvent;


/**
 * Extend the ExtbaseConfigurationManager to read YAML configurations.
 *
 * Scope: frontend / backend
 * @internal
 */
readonly class ConfigurationManager extends \TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager
{
    public function __construct(
        private YamlSource $yamlSource,
        #[Autowire(service: 'cache.assets')]
        private FrontendInterface $cache,
        private TypoScriptService $typoScriptService
    ) {
        parent::__construct(
            $yamlSource,
            $cache,
            $typoScriptService
        );
    }

    /**
     * Load and parse YAML files which are configured within the TypoScript
     * path plugin.tx_extensionkey.settings.yamlConfigurations
     *
     * The following steps will be done:
     *
     * * Convert each singe YAML file into an array
     * * merge this arrays together
     * * resolve all declared inheritances
     * * remove all keys if their values are NULL
     * * return all configuration paths within TYPO3.CMS
     * * sort by array keys, if all keys within the current nesting level are numerical keys
     * * resolve possible TypoScript settings in FE mode
     */
    public function getYamlConfiguration(array $typoScriptSettings, bool $isFrontend): array
    {

        $yamlConfiguration = parent::getYamlConfiguration($typoScriptSettings, $isFrontend);

        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $yamlConfiguration = $eventDispatcher
            ->dispatch(new AfterYamlConfigurationLoadedEvent($yamlConfiguration))
            ->getYamlConfiguration();

        return $yamlConfiguration;
    }
}
