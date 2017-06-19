<?php

namespace Drupal\Tests\sitefarm_core\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\BlockPlace;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Url;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Unit\Hooks\BlockPlace
 * @group sitefarm_core_hooks
 */
class BlockPlaceTest extends UnitTestCase {

  /**
   * The path matcher service.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * @var \Drupal\sitefarm_core\Hooks\BlockPlace
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->pathMatcher = $this->prophesize(PathMatcher::CLASS);
    $this->pathMatcher->isFrontPage()->willReturn(TRUE);

    $this->helper = new BlockPlace($this->pathMatcher->reveal());
  }

  /**
   * Tests attachBlockVisibilityJs method
   */
  public function testAttachBlockVisibilityJs() {
    $form = [];
    $this->helper->attachBlockVisibilityJs($form, 'block_form');

    $expected = [
      '#attached' => [
        'library' => [
          'sitefarm_core/sitefarm_core.blocks_path_visibility'
        ]
      ]
    ];
    $this->assertArrayEquals($expected, $form);
  }

  /**
   * Tests fixFrontPageLink method
   */
  public function testFixFrontPageLink() {
    $options = [
      'query' => [
        'block-place' => '1',
        'destination' => '/node',
      ]
    ];

    $url = $this->prophesize(Url::CLASS);
    $url->getOptions()->willReturn($options);

    $items = [
      'block_place' => [
        'tab' => [
          '#url' => $url->reveal()
        ]
      ]
    ];

    $this->helper->fixFrontPageLink($items);

    $result = $items['block_place']['tab']['#url']->toUriString();
    $expected = 'route:<front>?block-place=1&destination=/node';
    $this->assertEquals($expected, $result);
  }
}
