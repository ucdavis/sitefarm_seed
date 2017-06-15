<?php

namespace Drupal\Tests\sitefarm_core\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\BlockFormAlter;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\BlockFormAlter
 * @group sitefarm_core_hooks
 */
class AdvancedTabsGroupTest extends UnitTestCase {

  /**
   * @var \Drupal\sitefarm_core\Hooks\BlockFormAlter
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->helper = new BlockFormAlter();
  }

  /**
   * Tests hideVisibilityOptions() method
   *
   * @dataProvider hideVisibilityOptionsProvider
   */
  public function testHideVisibilityOptions($hide, $expected) {
    $form = [
      'visibility' => [
        'dummy' => '',
        'entity_bundle:block_content' => '',
        'entity_bundle:contact_message' => '',
        'entity_bundle:crop' => '',
        'entity_bundle:redirect' => '',
        'entity_bundle:scheduled_update' => '',
        'entity_bundle:shortcut' => '',
        'entity_bundle:menu_link_content' => '',
        'node_type' => '',
      ]
    ];
    $this->helper->hideVisibilityOptions($form, $hide);
    $this->assertArrayEquals($expected, $form);
  }

  /**
   * Provider for testHideVisibilityOptions()
   */
  public function hideVisibilityOptionsProvider() {
    return [
      [
        [],
        [
          'visibility' => ['dummy' => '']
        ]
      ],
      [
        ['dummy'],
        [
          'visibility' => [
            'entity_bundle:block_content' => '',
            'entity_bundle:contact_message' => '',
            'entity_bundle:crop' => '',
            'entity_bundle:redirect' => '',
            'entity_bundle:scheduled_update' => '',
            'entity_bundle:shortcut' => '',
            'entity_bundle:menu_link_content' => '',
            'node_type' => '',
          ]
        ]
      ],
    ];
  }

  /**
   * Tests movePathVisibilityToTop method
   */
  public function testMovePathVisibilityToTop() {
    $form = [
      'visibility' => [
        'visibility_tabs' => [],
        'entity_bundle' => [],
        'request_path' => [],
      ]
    ];
    $this->helper->movePathVisibilityToTop($form);

    $expected = [
      'visibility' => [
        'visibility_tabs' => [],
        'request_path' => [],
        'entity_bundle' => [],
      ]
    ];
    $this->assertArrayEquals($expected, $form);
  }
}
