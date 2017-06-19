<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\WidgetAlter;
use Drupal\Core\Form\FormStateInterface;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\WidgetAlter
 * @group sitefarm_core_hooks
 */
class WidgetAlterTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\sitefarm_core\Hooks\WidgetAlter
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->formState = $this->prophesize(FormStateInterface::CLASS);

    $this->helper = new WidgetAlter();

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->helper->setStringTranslation($translator);
  }

  /**
   * Tests setBodyFieldHelpText() method
   */
  public function testSetBodyFieldHelpText() {
    $element = [];
    $text = 'this is some text';
    $this->helper->setBodyFieldHelpText($element, $text);
    $this->assertEmpty($element);

    $element['#title'] = 'Body';
    $this->helper->setBodyFieldHelpText($element, $text);
    $this->assertEquals($text, $element['summary']['#description']);
  }

  /**
   * Tests setPrimaryImageTitleText() method
   */
  public function testSetPrimaryImageTitleText() {
    $element = [];
    $this->helper->setPrimaryImageTitleText($element);
    $this->assertEmpty($element);

    $element['#field_name'] = 'field_sf_primary_image';
    $this->helper->setPrimaryImageTitleText($element);
    $this->assertInstanceOf('Drupal\sitefarm_core\Hooks\WidgetAlter', $element['#process'][0][0]);
    $this->assertEquals('primaryImageTitleProcess', $element['#process'][0][1]);
  }

  /**
   * Tests primaryImageTitleProcess() method
   */
  public function testPrimaryImageTitleProcess() {
    $element = [];
    $form = [];
    $expected = [
      'title' => [
        '#title' => 'Caption',
        '#description' => ''
      ]
    ];
    $return = $this->helper->primaryImageTitleProcess($element, $this->formState->reveal(), $form);
    $this->assertArrayEquals($expected, $return);
  }

  /**
   * Tests setFocalPointHelpText() method
   */
  public function testSetFocalPointHelpText() {
    // No element
    $element = [];
    $this->helper->setFocalPointHelpText($element);
    $this->assertEmpty($element);

    // FocalPointImageWidget not being processed
    $element['#process'] = ['test'];
    $expected = ['#process' => ['test']];
    $this->helper->setFocalPointHelpText($element);
    $this->assertArrayEquals($expected, $element);

    // Focal Point will be used
    $element['#process'] = [
      ['Drupal\focal_point\Plugin\Field\FieldWidget\FocalPointImageWidget']
    ];
    $this->helper->setFocalPointHelpText($element);
    $this->assertInstanceOf('Drupal\sitefarm_core\Hooks\WidgetAlter', $element['#process'][1][0]);
    $this->assertEquals('focalPointHelpProcess', $element['#process'][1][1]);
  }

  /**
   * Tests focalPointHelpProcess() method
   */
  public function testFocalPointHelpProcess() {
    $form = [];

    // No element
    $element = [];
    $return = $this->helper->focalPointHelpProcess($element, $this->formState->reveal(), $form);
    $this->assertEmpty($return);

    // Access is denied
    $element['alt']['#access'] = FALSE;
    $return = $this->helper->focalPointHelpProcess($element, $this->formState->reveal(), $form);
    $this->assertArrayEquals($element, $return);

    // Access granted
    $element = [
      'alt' => [
        '#access' => TRUE,
        '#weight' => 10,
      ],
    ];

    $expected = [
      'alt' => [
        '#access' => TRUE,
        '#weight' => 10,
      ],
      'focal_point_how' => [
        '#markup' => '<p><strong>What\'s the plus sign for? </strong>Wherever the crosshair is placed is guaranteed to be in any cropped image.</p>',
        '#weight' => 9,
      ]
    ];
    $return = $this->helper->focalPointHelpProcess($element, $this->formState->reveal(), $form);
    $this->assertArrayEquals($expected, $return);

  }

}
