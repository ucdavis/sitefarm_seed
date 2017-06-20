<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\FormAlter;
use Drupal\sitefarm_core\AdvancedTabsGroup;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\FormAlter
 * @group sitefarm_core_hooks
 */
class FormAlterTest extends UnitTestCase {

  /**
   * The path matcher service.
   *
   * @var \Drupal\sitefarm_core\AdvancedTabsGroup
   */
  protected $advancedTabsGroup;

  /**
   * @var \Drupal\sitefarm_core\Hooks\FormAlter
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->advancedTabsGroup = $this->prophesize(AdvancedTabsGroup::CLASS);
    $this->advancedTabsGroup->loadForm([])->willReturn($this->advancedTabsGroup->reveal());

    $this->helper = new FormAlter($this->advancedTabsGroup->reveal());

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->helper->setStringTranslation($translator);
  }

  /**
   * Tests the moveFeaturedToOptionsGroup() method
   */
  public function testMoveFeaturedToOptionsGroup() {
    $this->advancedTabsGroup->loadForm([])->shouldBeCalled();
    $this->advancedTabsGroup->moveField('field_sf_featured_status', 'options')->shouldBeCalled();
    $this->advancedTabsGroup->save()->shouldBeCalled();

    $form = [];
    $this->helper->moveFeaturedToOptionsGroup($form);
  }

  /**
   * Tests the createCategorizingGroup() method
   */
  public function testCreateCategorizingGroup() {
    $this->advancedTabsGroup->loadForm([])->shouldBeCalled();
    $this->advancedTabsGroup->createGroup('categorizing', 'Categorizing', 94)->shouldBeCalled();
    $this->advancedTabsGroup->save()->shouldBeCalled();

    $form = [];
    $this->helper->createCategorizingGroup($form);
  }

  /**
   * Tests the moveTagsToCategorizingGroup() method
   */
  public function testMoveTagsToCategorizingGroup() {
    $this->advancedTabsGroup->loadForm([])->shouldBeCalled();
    $this->advancedTabsGroup->moveField('field_sf_tags', 'categorizing')->shouldBeCalled();
    $this->advancedTabsGroup->save()->shouldBeCalled();

    $form = [];
    $this->helper->moveTagsToCategorizingGroup($form);
  }

  /**
   * Tests the setAddAnotherItemLabel() method
   */
  public function testSetAddAnotherItemLabel() {
    $text = 'This is some Text';

    // form is empty
    $form = [];
    $field_name = 'test_field';
    $return = $this->helper->setAddAnotherItemLabel($form, $field_name, $text);
    $this->assertEmpty($return);

    // The field is not found on the form
    $form = ['test_field' => []];
    $field_name = 'wrong_field';
    $return = $this->helper->setAddAnotherItemLabel($form, $field_name, $text);
    $this->assertArrayEquals($form, $return);

    // The field is correctly found on the form with all needed arrays
    $form['test_field']['widget']['add_more']['#value'] = 'test';
    $field_name = 'test_field';
    $return = $this->helper->setAddAnotherItemLabel($form, $field_name, $text);
    $this->assertEquals($text, $return['test_field']['widget']['add_more']['#value']);
  }

}
