<?php

namespace Drupal\sitefarm_custom_social_links\Plugin\SocialMediaLinks\Iconset;

use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetInterface;

/**
 * Provides 'sitefarm_custom_social_links' iconset.
 *
 * @Iconset(
 *   id = "sitefarm_custom_social_links",
 *   name = "Sitefarm Custom Social Links",
 * )
 */
class SiteFarmIconSet extends IconsetBase implements IconsetInterface {

  /**
   * @return string
   *
   * @codeCoverageIgnore
   */
  public function getPath() {
    return drupal_get_path("module", "sitefarm_custom_social_links");
  }

  /**
   * @return array
   */
  public function getStyle() {
    return array(
      'default' => 'default',
    );
  }

  /**
   * @param string $iconName
   * @param string $style
   * @return string
   */
  public function getIconPath($iconName, $style) {
    return $this->getPath() . '/img/' . $iconName . '.svg';
  }
}