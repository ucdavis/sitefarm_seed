<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks\Mocks;

use Drupal\sitefarm_core\Hooks\Themes;

/**
 * Overrides methods which have global functions
 */
class MockThemes extends Themes {

  /**
   * Method wrapper of drupal_flush_all_caches so that it can be mocked for Unit
   * Tests
   *
   * @return void
   */
  public function flushAllCaches() {
    // Caches flushed
    return;
  }

  /**
   * Get the testable theme path since the parent uses globals
   *
   * @param $theme_name
   * @return string
   */
  protected function getThemePath($theme_name) {
    return __DIR__ . '/' .  $theme_name;
  }
}
