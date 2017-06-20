<?php

namespace Drupal\sitefarm_core\Hooks;

/**
 * Class BlockFormAlter.
 *
 * Helper utility to break out block form alter procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class BlockFormAlter {

  /**
   * Hide visibility options on the block instance configuration page
   *
   * @param $form
   * @param array $hide
   */
  public function hideVisibilityOptions(&$form, $hide = []) {
    // Hide the following visibility options
    if (empty($hide)) {
      $hide = [
        'entity_bundle:block_content',
        'entity_bundle:contact_message',
        'entity_bundle:crop',
        'entity_bundle:redirect',
        'entity_bundle:scheduled_update',
        'entity_bundle:shortcut',
        'entity_bundle:menu_link_content',
        'node_type',
      ];
    }

    foreach ($hide as $name) {
      unset($form['visibility'][$name]);
    }
  }

  /**
   * Move the path visibility to the top on block instance configuration pages
   *
   * @param $form
   */
  public function movePathVisibilityToTop(&$form) {
    if (isset($form['visibility']['request_path'])) {
      // Move the path to the top of the array
      $form['visibility'] = array('request_path' => $form['visibility']['request_path']) + $form['visibility'];
      // Move the visibility tabs back to the top so that everything below it renders
      $form['visibility'] = array('visibility_tabs' => $form['visibility']['visibility_tabs']) + $form['visibility'];
    }
  }

}
