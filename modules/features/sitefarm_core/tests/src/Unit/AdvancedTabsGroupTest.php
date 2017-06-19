<?php

namespace Drupal\Tests\sitefarm_core\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\AdvancedTabsGroup;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\AdvancedTabsGroup
 * @group sitefarm_core
 */
class AdvancedTabsGroupTest extends UnitTestCase
{

  /**
   * @var \Drupal\sitefarm_core\AdvancedTabsGroup
   */
  protected $groupObj;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->groupObj = new AdvancedTabsGroup();

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->groupObj->setStringTranslation($translator);
  }

  /**
   * Tests the loadForm method
   */
  public function testLoadForm() {
    $form = ['test'];

    $return = $this->groupObj->loadForm($form);

    $this->assertInstanceOf('Drupal\sitefarm_core\AdvancedTabsGroup', $return);
    $this->assertArrayEquals($form, $return->save());
  }

  /**
   * Test the createGroup method
   *
   * @dataProvider createGroupProvider
   */
  public function testCreateGroup($form, $expected) {
    $machine_name = 'machine_test';
    $title = 'title_test';

    $this->groupObj->loadForm($form);

    $this->groupObj->createGroup($machine_name, $title);
    $return = $this->groupObj->save();

    $this->assertArrayEquals($expected, $return);
  }

  /**
   * Provider for testCreateGroup()
   */
  public function createGroupProvider() {
    $advanced =  [
      '#type' => 'vertical_tabs'
    ];

    return [
      [['test'], ['test']],
      [
        [
          'advanced' => $advanced,
          'machine_test' => 'test'
        ],
        [
          'advanced' => $advanced,
          'machine_test' => 'test'
        ]
      ],
      [
        [
          'advanced' => $advanced,
        ],
        [
          'advanced' => $advanced,
          'machine_test' => [
            '#type' => 'details',
            '#title' => 'title_test',
            '#group' => 'advanced',
            '#weight' => 90,
            '#optional' => TRUE,
          ]
        ]
      ]
    ];
  }

  /**
   * Tests moveField method when no advanced tabs group is available.
   *
   * @covers ::movefield()
   */
  public function testMoveFieldHasNoAdvancedTabs() {
    $field_name = 'field_test';
    $group_name = 'group_test';

    $form = ['test'];
    $this->groupObj->loadForm($form);

    $this->groupObj->moveField($field_name, $group_name);
    $return = $this->groupObj->save();

    $this->assertArrayEquals($form, $return);
  }

  /**
   * Tests moveField method with advanced tabs available.
   *
   * @covers ::movefield()
   */
  public function testMoveFieldHasAdvancedTabs() {
    $field_name = 'field_test';
    $group_name = 'group_test';

    $form = [
      'advanced' => [
        '#type' => 'vertical_tabs'
      ],
      $field_name => [],
    ];
    $this->groupObj->loadForm($form);

    $this->groupObj->moveField($field_name, $group_name);
    $return = $this->groupObj->save();

    $this->assertEquals('group_test', $return[$field_name]['#group']);
  }

  /**
   * Tests the hasAdvancedTabs method
   *
   * @dataProvider advancedTabsProvider
   */
  public function testHasAdvancedTabs($form, $expected) {
    $this->groupObj->loadForm($form);

    $return = $this->groupObj->hasAdvancedTabs();
    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testHasAdvancedTabs()
   */
  public function advancedTabsProvider() {
    return [
      [[], FALSE],
      [['test' => 'test'], FALSE],
      [['advanced' => 'test'], FALSE],
      [['advanced' => ['#type' => 'test']], FALSE],
      [['advanced' => ['#type' => 'vertical_tabs']], TRUE],
    ];
  }
}
