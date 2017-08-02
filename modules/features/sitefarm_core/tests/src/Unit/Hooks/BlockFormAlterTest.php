<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\BlockFormAlter;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\BlockFormAlter
 * @group sitefarm_core_hooks
 */
class BlockFormAlterTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Drupal\sitefarm_core\Hooks\BlockFormAlter
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->configFactory = $this->getConfigFactoryStub([
      'sitefarm_core.settings' => [
        'block_visibility_hidden' => [
          'entity_bundle:block_content',
          'entity_bundle:contact_message',
          'entity_bundle:crop',
          'entity_bundle:redirect',
          'entity_bundle:scheduled_update',
          'entity_bundle:shortcut',
          'entity_bundle:menu_link_content',
          'node_type',
        ],
        'block_views_contextual_hidden' => [
          'nid'
        ]
      ]
    ]);

    $this->helper = new BlockFormAlter($this->configFactory);
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

  /**
   * Tests removeViewsContextualElements method
   */
  public function testRemoveViewsContextualElements() {
    $form = [
      'settings' => [
        'context_mapping' => [
          'nid' => [
            '#access' => TRUE,
          ]
        ],
      ]
    ];
    $this->helper->removeViewsContextualElements($form, 'block_form');

    $expected = $form['settings']['context_mapping']['nid']['#access'];
    $this->assertFalse($expected);
  }
}
