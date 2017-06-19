<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\Themes;
use Drupal\Tests\sitefarm_core\Unit\Hooks\Mocks\MockThemes;
use Drupal\config_update\ConfigReverter;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\Themes
 * @group sitefarm_core_hooks
 */
class ThemesTest extends UnitTestCase {

  /**
   * The configuration reverter service.
   *
   * @var \Drupal\config_update\ConfigReverter
   */
  protected $configReverter;

  /**
   * @var \Drupal\sitefarm_core\Hooks\Themes
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->configReverter = $this->prophesize(ConfigReverter::CLASS);

    // Use the MockThemes class since the original has methods with globals
    $this->helper = new MockThemes($this->configReverter->reveal());
  }

  /**
   * Tests the revertSitefarmImageStylesOnInstall() method
   */
  public function testRevertSitefarmImageStylesOnInstall() {
    $this->configReverter->revert('image_style', 'sf_test')->shouldBeCalled();

    $theme_list = [
      'bartik',
      'mock_theme'
    ];
    $this->helper->revertSitefarmImageStylesOnInstall($theme_list);
  }

}
