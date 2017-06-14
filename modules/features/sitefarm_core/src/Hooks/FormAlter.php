<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\sitefarm_core\AdvancedTabsGroup;

/**
 * Class FormAlter.
 *
 * Helper utility to break out procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class FormAlter {
  // Ensure we have the ability to use string translation $this->t().
  use StringTranslationTrait;

  /**
   * The advanced tabs group service.
   *
   * @var \Drupal\sitefarm_core\AdvancedTabsGroup
   */
  protected $advancedTabsGroup;

  /**
   * FormAlter constructor.
   * @param \Drupal\sitefarm_core\AdvancedTabsGroup $advancedTabsGroup
   */
  public function __construct(AdvancedTabsGroup $advancedTabsGroup) {
    $this->advancedTabsGroup = $advancedTabsGroup;
  }

  /**
   * Move the Feature Content field to the Promotion Options group
   *
   * @param $form
   * @return array
   */
  public function moveFeaturedToOptionsGroup(&$form) {
    $advanced_tabs = $this->advancedTabsGroup->loadForm($form);
    $advanced_tabs->moveField('field_sf_featured_status', 'options');
    $form = $advanced_tabs->save();
  }

  /**
   * Create a Categorizing tray for types with taxonomy references
   *
   * @param $form
   * @return array
   */
  public function createCategorizingGroup(&$form) {
    $advanced_tabs = $this->advancedTabsGroup->loadForm($form);
    $advanced_tabs->createGroup('categorizing', 'Categorizing', 94);
    $form = $advanced_tabs->save();
  }

  /**
   * Move the tags field to the Categorization group
   *
   * @param $form
   * @return array
   */
  public function moveTagsToCategorizingGroup(&$form) {
    $advanced_tabs = $this->advancedTabsGroup->loadForm($form);
    $advanced_tabs->moveField('field_sf_tags', 'categorizing');
    $form = $advanced_tabs->save();
  }

  /**
   * Alter the 'Add another item' text on Multiple entry fields
   *
   * @param $form
   * @param $field_name
   * @param $text
   * @return mixed
   */
  public function setAddAnotherItemLabel(&$form, $field_name, $text) {
    if (isset($form[$field_name]['widget']['add_more']['#value'])) {
      $form[$field_name]['widget']['add_more']['#value'] = $this->t($text);
    }
    return $form;
  }

}
