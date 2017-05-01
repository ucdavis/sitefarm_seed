<?php

namespace Drupal\Tests\site_credits\Unit\Plugin\Block;

use Drupal\site_credits\Plugin\Block\SiteCreditsBlock;

/**
 * Overrides methods which have global functions
 */
class TestSiteCreditsBlock extends SiteCreditsBlock {

  /**
   * Method wrapper of theme_get_settings so that it can be mocked for Unit Test
   *
   * @return string
   */
  protected function getLogoPath() {
    return 'theme_logo_url';
  }
}
