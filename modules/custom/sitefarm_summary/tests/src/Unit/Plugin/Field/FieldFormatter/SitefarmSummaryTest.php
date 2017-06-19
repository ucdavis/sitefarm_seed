<?php

namespace Drupal\Tests\sitefarm_summary\Unit\Plugin\Field\FieldFormatter;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_summary\Plugin\Field\FieldFormatter\SitefarmSummary;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\DataDefinitionInterface;

/**
 * @coversDefaultClass \Drupal\sitefarm_summary\Plugin\Field\FieldFormatter\SitefarmSummary
 * @group sitefarm_summary
 */
class SitefarmSummaryTest extends UnitTestCase
{
  /**
   * @var \Drupal\sitefarm_summary\Plugin\Field\FieldFormatter\SitefarmSummary
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUP();

    $plugin_id = 'sitefarm_summary_field';
    $plugin_definition['provider'] = 'sitefarm_summary';
    $field_definition = $this->prophesize(FieldDefinitionInterface::CLASS);
    $settings = [];
    $label = 'test label';
    $view_mode = 'full';
    $third_party_settings = [];

    $this->plugin = new SitefarmSummary(
      $plugin_id,
      $plugin_definition,
      $field_definition->reveal(),
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Test the settingsSummary() method
   */
  public function testSettingsSummary()
  {
    $expected = ['Displays the summary only for text fields that have a summary.'];

    $this->assertArrayEquals($expected, $this->plugin->settingsSummary());
  }

  /**
   * Test the viewElements() method
   */
  public function testViewElements()
  {
    $expected = [
      'test_field' => [
        '#type' => 'markup',
        '#markup' => 'summary text',
      ]
    ];

    $item = new \stdClass();
    $item->summary = 'summary text';

    $definition = $this->prophesize(DataDefinitionInterface::CLASS);
    $items = new FieldItemList($definition->reveal());

    // Use reflection to alter the protected $items->list
    $reflectionObject = new \ReflectionObject($items);
    $property = $reflectionObject->getProperty('list');
    $property->setAccessible(true);
    $property->setValue($items, ['test_field' => $item]);

    $return = $this->plugin->viewElements($items, 'en');
    $this->assertArrayEquals($expected, $return);
  }

}
