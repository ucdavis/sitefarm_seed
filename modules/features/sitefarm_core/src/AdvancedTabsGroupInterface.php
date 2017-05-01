<?php

namespace Drupal\sitefarm_core;

/**
 * Interface AdvancedTabsGroupInterface.
 *
 * @package Drupal\sitefarm_core
 */
interface AdvancedTabsGroupInterface {

  /**
   * Pass in the current form by reference
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   */
  public function loadForm(array $form);

  /**
   * Create advanced group in node sidebar
   *
   * @param $machine_name
   * @param $title
   * @param $weight
   *   Integer of the weight of the form element
   */
  public function createGroup($machine_name, $title, $weight = 90);

  /**
   * Move a field into a group in the Advanced sidebar
   *
   * @param $field_name
   * @param $group_name
   *   Machine name for the Advanced group the field should be moved to
   */
  public function moveField($field_name, $group_name);

  /**
   * Check that this form has advanced tabs available (AKA: Node Edit form)
   *
   * @return bool
   */
  public function hasAdvancedTabs();

  /**
   * Save the form and return it.
   *
   * @return array
   */
  public function save();

}
