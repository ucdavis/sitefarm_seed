<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Serialization\Yaml;

/**
 * Class Themes.
 *
 * Helper utility for themes to break out procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class Themes {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  protected $configFactory;

  /**
   * Themes constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config Factory service.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Revert Sitefarm Image styles having a sf_ prefix.
   *
   * Each theme needs the ability to override image styles so that they can
   * match the style for the custom theme. In addition, subthemes may want to
   * alter an image style. This searches for any sitefarm image style config in
   * a newly installed theme by looking for a prefix of "sf_".
   *
   * @param array $theme_list
   */
  public function revertSitefarmImageStylesOnInstall(array $theme_list) {
    // Loop through each of the newly installed themes
    foreach ($theme_list as $theme_name) {
      // Exit if the theme does not have config
      $theme_config_path = $this->getThemePath($theme_name) . '/config/install';
      if (!file_exists($theme_config_path)) {
        continue;
      }

      // Get all config files
      $config_files = scandir($theme_config_path);
      $image_style_files = preg_grep('/image\.style\.sf_/', $config_files);

      foreach ($image_style_files as $image_style) {
        $data = $this->getYamlData($theme_config_path . '/' . $image_style);
        $config_name = str_replace('.yml', '', $image_style);

        $config = $this->configFactory->getEditable($config_name);
        if ($config && $data) {
          $config->setData($data)
            ->save();
        }
      }
    }

    // Flush all caches on theme install to fix multiple errors
    $this->flushAllCaches();
  }

  /**
   * Get an array of data from a Yaml config file.
   *
   * @param $file
   *   The complete path for the config file.
   *
   * @return array|bool
   */
  public function getYamlData($file) {
    $raw = file_get_contents($file);
    $data = Yaml::decode($raw);
    // A simple string is valid YAML for any reason.
    if (!is_array($data)) {
      return FALSE;
    }
    return $data;
  }

  /**
   * Wrapper method for the procedural drupal_flush_all_caches().
   *
   * @codeCoverageIgnore
   */
  public function flushAllCaches() {
    drupal_flush_all_caches();
  }

  /**
   * Get the theme path
   *
   * @param $theme_name
   * @return string
   *
   * @codeCoverageIgnore
   */
  protected function getThemePath($theme_name) {
    return DRUPAL_ROOT . base_path() . drupal_get_path('theme', $theme_name);
  }

}
