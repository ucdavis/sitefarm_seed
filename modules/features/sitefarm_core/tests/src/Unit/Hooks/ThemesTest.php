<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Tests\sitefarm_core\Unit\Hooks\Mocks\MockThemes;
use Drupal\Core\Config\Config;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\Themes
 * @group sitefarm_core_hooks
 */
class ThemesTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Store the output of a config save for comparison.
   */
  protected $configOutput = [
    'status' => 'false',
  ];

  /**
   * @var \Drupal\sitefarm_core\Hooks\Themes
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $self = $this;

    $config = $this->prophesize(Config::CLASS);
    $config->setData(Argument::type('array'))->will(function ($args) use ($self, $config) {
      $self->configOutput = $args[0];
      return $config;
    });
    $config->save()->willReturn();
    $this->configFactory = $this->prophesize(ConfigFactoryInterface::CLASS);
    $this->configFactory->getEditable('image.style.sf_test')->willReturn($config);

    // Use the MockThemes class since the original has methods with globals
    $this->helper = new MockThemes($this->configFactory->reveal());
  }

  /**
   * Tests the revertSitefarmImageStylesOnInstall() method
   */
  public function testRevertSitefarmImageStylesOnInstall() {
    $theme_list = [
      'bartik',
      'mock_theme'
    ];
    $this->helper->revertSitefarmImageStylesOnInstall($theme_list);
    $this->assertTRUE($this->configOutput['status']);
  }

  /**
   * Tests the getYamlData() method.
   */
  public function testGetYamlData() {
    $file = __DIR__ . '/Mocks/mock_theme/config/install/image.style.sf_test.yml';
    $return = $this->helper->getYamlData($file);
    $this->assertEquals('sf_test', $return['name']);
  }

}
