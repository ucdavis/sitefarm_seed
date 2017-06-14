<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class NodeFormAlter.
 *
 * Helper utility to break out node form alter procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class NodeFormAlter {
  // Ensure we have the ability to use string translation $this->t().
  use StringTranslationTrait;

  /**
   * Remove the menu 'Weight' field so that it doesn't confuse people
   *
   * @param array $form
   */
  public function removeMenuWeight(&$form) {
    if (isset($form['menu'])) {
      $form['menu']['link']['weight']['#access'] = FALSE;
    }
  }

  /**
   * Change the Meta Tags field label
   *
   * @param array $form
   * @param string $title
   */
  public function setMetaTagsTitle(&$form, $title) {
    if (isset($form['field_sf_meta_tags'])) {
      $form['field_sf_meta_tags']['widget'][0]['#title'] = $this->t($title);
    }
  }

  /**
   * Attach javascript to ensure that required javascript fields don't go under
   * the admin toolbar
   *
   * @param array $form
   */
  public function attachToolbarHidingPreventionJs(&$form) {
    $form['#attached']['library'][] = 'sitefarm_core/sitefarm_core.required_fields';
  }

  /**
   * Add a label to the node edit sidebar
   *
   * @param array $form
   * @param string $title
   */
  public function addTitleToSidebar(&$form, $title) {
    $form['advanced']['#prefix'] = '<h2>' . $this->t($title) . '</h2>';
  }
}
