<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:11
 */

namespace DataBundle\Services\Theme;


use DataBundle\Entity\Theme;

interface ThemeServiceInterface
{
    /**
     * Synchronizes the themes from the filesystem
     */
    public function syncThemes(): void;

    /**
     * Saves the given theme configuration
     *
     * @param array $config
     * @param string $themeName
     */
    public function saveConfig(array $config, string $themeName): void;

    /**
     * Gets the theme by the given name
     *
     * @param string $name
     * @return Theme
     */
    public function getTheme(string $name): Theme;

    /**
     * Gets the default theme
     *
     * @return Theme
     */
    public function getDefaultJinyaTheme(): Theme;

    /**
     * Gets all themes
     *
     * @return Theme[]
     */
    public function getAllThemes(): array;
}