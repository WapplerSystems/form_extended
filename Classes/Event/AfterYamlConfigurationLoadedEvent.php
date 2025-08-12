<?php

namespace WapplerSystems\FormExtended\Event;

final class AfterYamlConfigurationLoadedEvent
{

    private array $yamlConfiguration;

    public function __construct($yamlConfiguration)
    {
        $this->yamlConfiguration = $yamlConfiguration;
    }

    public function getYamlConfiguration() : array {
        return $this->yamlConfiguration;
    }

    public function setYamlConfiguration(array $yamlConfiguration): void
    {
        $this->yamlConfiguration = $yamlConfiguration;
    }

}
