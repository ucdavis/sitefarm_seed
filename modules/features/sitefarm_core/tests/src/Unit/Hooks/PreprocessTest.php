<?php

namespace Drupal\Tests\sitefarm_core\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\Preprocess;
use Drupal\Core\Extension\ModuleHandler;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Unit\Hooks\Preprocess
 * @group sitefarm_core_hooks
 */
class PreprocessTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $ModuleHandler;

  /**
   * @var \Drupal\sitefarm_core\Hooks\Preprocess
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->ModuleHandler = $this->prophesize(ModuleHandler::CLASS);

    $this->helper = new Preprocess($this->ModuleHandler->reveal());
  }

  /**
   * Tests fixMetatagFrontTitle method
   */
  public function testFixMetatagFrontTitle() {
    $this->ModuleHandler->moduleExists('metatag')->willReturn(TRUE);

    $variables = [];
    $variables['head_title']['title'] = '| This is my title';
    $this->helper->fixMetatagFrontTitle($variables);

    $expected = 'This is my title';
    $this->assertEquals($expected, $variables['head_title']['title']);
  }

}
