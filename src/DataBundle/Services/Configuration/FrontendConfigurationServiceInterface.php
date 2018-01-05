<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 18:02
 */

namespace DataBundle\Services\Configuration;


use DataBundle\Entity\FrontendConfiguration;

interface FrontendConfigurationServiceInterface
{
    /**
     * Gets the frontend configuration
     *
     * @return FrontendConfiguration
     */
    public function getConfig(): FrontendConfiguration;

    /**
     * Saves the frontend configuration
     *
     * @param FrontendConfiguration $configuration
     */
    public function writeConfig(FrontendConfiguration $configuration): void;
}