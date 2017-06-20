<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\NodeFormAlter;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\NodeFormAlter
 * @group sitefarm_core_hooks
 */
class NodeFormAlterTest extends UnitTestCase {

  /**
   * @var \Drupal\sitefarm_core\Hooks\NodeFormAlter
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->helper = new NodeFormAlter();

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->helper->setStringTranslation($translator);
  }

  /**
   * Tests removeMenuWeight() method
   */
  public function testRemoveMenuWeight() {
    $form = [];
    $this->helper->removeMenuWeight($form);
    $this->assertEmpty($form);

    $form['menu']['link']['weight']['#access'] = TRUE;
    $this->helper->removeMenuWeight($form);
    $this->assertFalse($form['menu']['link']['weight']['#access']);
  }

  /**
   * Tests setMetaTagsTitle() method
   */
  public function testSetMetaTagsTitle() {
    $form = [];
    $title = 'this is a title';
    $this->helper->setMetaTagsTitle($form, $title);
    $this->assertEmpty($form);

    $form['field_sf_meta_tags']['widget'][0]['#title'] = 'test';
    $this->helper->setMetaTagsTitle($form, $title);
    $this->assertEquals($title, $form['field_sf_meta_tags']['widget'][0]['#title']);
  }

  /**
   * Tests attachToolbarHidingPreventionJs() method
   */
  public function testAttachToolbarHidingPreventionJs() {
    $form = [];
    $this->helper->attachToolbarHidingPreventionJs($form);

    $expected = [
      '#attached' => [
        'library' => [
          'sitefarm_core/sitefarm_core.required_fields'
        ]
      ]
    ];
    $this->assertArrayEquals($expected, $form);
  }

  /**
   * Tests addTitleToSidebar() method
   */
  public function testAddTitleToSidebar() {
    $form = [];
    $title = 'This is a Title';
    $this->helper->addTitleToSidebar($form, $title);

    $expected = [
      'advanced' => [
        '#prefix' => '<h2>' . $title . '</h2>'
      ]
    ];
    $this->assertArrayEquals($expected, $form);
  }

}
