<?php

namespace Drupal\Tests\sitefarm_event\Unit\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_event\Plugin\Field\FieldFormatter\DateRangeMergeFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @coversDefaultClass \Drupal\sitefarm_event\Plugin\Field\FieldFormatter\DateRangeMergeFormatter
 * @group sitefarm_event
 */
class DateRangeMergeFormatterTest extends UnitTestCase
{
  /**
   * @var \Drupal\sitefarm_event\Plugin\Field\FieldFormatter\DateRangeMergeFormatter
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUP();

    $plugin_id = 'sitefarm_event_field';
    $plugin_definition['provider'] = 'sitefarm_event';
    $field_definition = $this->prophesize(FieldDefinitionInterface::CLASS);
    $settings = [];
    $label = 'test label';
    $view_mode = 'full';
    $third_party_settings = [];
    $date_formatter = $this->prophesize(DateFormatterInterface::CLASS);
    $date_format_storage = $this->prophesize(EntityStorageInterface::CLASS);

    $this->plugin = new DateRangeMergeFormatter(
      $plugin_id,
      $plugin_definition,
      $field_definition->reveal(),
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
      $date_formatter->reveal(),
      $date_format_storage->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Test the viewElements() method
   *
   * TODO: Figure out how to actually test this
   */
//  public function testViewElements() {
//    $expected = [
//      'test_field' => 'test'
//    ];
//
//    $start_date = $this->prophesize(DrupalDateTime::CLASS);
//    $end_date = $this->prophesize(DrupalDateTime::CLASS);
//
//    $item = new \stdClass();
//    $item->start_date = $start_date->reveal();
//    $item->end_date = $end_date->reveal();
//
//    $definition = $this->prophesize(DataDefinitionInterface::CLASS);
//    $items = new FieldItemList($definition->reveal());
//
//    // Use reflection to alter the protected $items->list
//    $reflectionObject = new \ReflectionObject($items);
//    $property = $reflectionObject->getProperty('list');
//    $property->setAccessible(true);
//    $property->setValue($items, ['test_field' => $item]);
//
//    // Use reflection to alter the protected $items->list
//    $reflectionObject = new \ReflectionObject($items);
//    $property = $reflectionObject->getProperty('list');
//    $property->setAccessible(true);
//    $property->setValue($items, ['test_field' => $item]);
//
//    $return = $this->plugin->viewElements($items, 'en');
//    $this->assertArrayEquals($expected, $return);
//  }

  /**
   * Tests the settingsForm method
   */
  public function testSettingsForm() {
    $form = [];
    $form_state = $this->prophesize(FormStateInterface::CLASS);

    $return = $this->plugin->settingsForm($form, $form_state->reveal());

    $this->assertTrue($return['show_day_name']['#default_value']);
    $this->assertTrue($return['show_time']['#default_value']);
    $this->assertEquals(' ~ ', $return['separator']['#default_value']);

    // Altered config
    $this->plugin->setSettings([
      'show_day_name' => FALSE,
      'show_time' => FALSE,
      'separator' => '-',
    ]);
    $return = $this->plugin->settingsForm($form, $form_state->reveal());

    $this->assertFalse($return['show_day_name']['#default_value']);
    $this->assertFalse($return['show_time']['#default_value']);
    $this->assertEquals('-', $return['separator']['#default_value']);
  }

  /**
   * Test the settingsSummary() method
   */
  public function testSettingsSummary() {
    $expected = [
      'Display Day Name: Yes',
      'Display Time: Yes',
//      'Separator: %separator'
    ];

    $return = $this->plugin->settingsSummary();

    $this->assertEquals($expected[0], $return[0]);
    $this->assertEquals($expected[1], $return[1]);
//    $this->assertEquals($expected[2], $this->plugin->settingsSummary()[2]);
  }
}
