<?php

namespace Drupal\sitefarm_core;

/**
 * Class AdvancedTabsGroup.
 *
 * Alter the Node edit form so that fields can be added into the Advanced Group.
 *
 * @package Drupal\sitefarm_core
 */
class AdvancedTabsGroup implements AdvancedTabsGroupInterface {

  /**
   * @var $form
   */
  protected $form;

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public function loadForm(array $form) {
    $this->form = $form;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function createGroup($machine_name, $title, $weight = 90) {
    // First check that this form has advanced tabs
    if (!$this->hasAdvancedTabs()) {
      return;
    }

    // Check that the group doesn't already exist
    if (isset($this->form[$machine_name])) {
      return;
    }

    // Create the new group
    $this->form[$machine_name] = array(
      '#type' => 'details',
      '#title' => t($title),
      '#group' => 'advanced',
      '#weight' => $weight,
      '#optional' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function moveField($field_name, $group_name) {
    // First check that this form has advanced tabs
    if (!$this->hasAdvancedTabs()) {
      return;
    }

    if (isset($this->form[$field_name])) {
      $this->form[$field_name]['#group'] = $group_name;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function hasAdvancedTabs() {
    if (isset($this->form['advanced']['#type']) && $this->form['advanced']['#type'] == 'vertical_tabs') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    return $this->form;
  }

}
