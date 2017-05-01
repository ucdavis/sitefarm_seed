<?php

namespace Drupal\Tests\sitefarm_custom_social_links\Unit\Plugin\SocialMediaLinks\Iconset;

use Drupal\sitefarm_custom_social_links\Plugin\SocialMediaLinks\Iconset\SiteFarmIconSet;

/**
 * Extend SiteFarmIconSet so that global functions can be replaced in testing
 */
class TestSiteFarmIconSet extends SiteFarmIconSet {

  /**
   * @return string
   */
  public function getPath() {
    return 'path';
  }
}