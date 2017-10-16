<?php

namespace Drupal\sitefarm_core\Hooks;
use Drupal\config_update\ConfigReverter;

/**
 * Class Themes.
 *
 * Helper utility for themes to break out procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class Themes {

  /**
   * The configuration reverter service.
   *
   * @var \Drupal\config_update\ConfigReverter
   */
  protected $configReverter;

  /**
   * Themes constructor.
   *
   * @param \Drupal\config_update\ConfigReverter $configReverter
   */
  public function __construct(ConfigReverter $configReverter) {
    $this->configReverter = $configReverter;
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

      // If the new theme has SiteFarm image styles we want to revert to them
      if ($image_style_files) {
        $revert_styles = preg_replace('/^image\.style\.(sf_[^\.]+)\.yml$/', '$1', $image_style_files);

        // Revert the image styles in the sitefarm_one theme
        foreach ($revert_styles as $image_style) {
          $this->configReverter->revert('image_style', $image_style);
        }
      }
    }

    // Flush all caches on theme install to fix multiple errors
    $this->flushAllCaches();
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
