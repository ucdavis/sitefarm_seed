<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\sitefarm_core\BlockConfigFormHelpers;

/**
 * Class BlockFormAlter.
 *
 * Helper utility to break out block form alter procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class BlockFormAlter {
  // Ensure we have the ability to use string translation $this->t().
  use StringTranslationTrait;

  /**
   * The advanced tabs group service.
   *
   * @var \Drupal\sitefarm_core\BlockConfigFormHelpers
   */
  protected $blockConfigFormHelpers;

  /**
   * BlockFormAlter constructor.
   * @param \Drupal\sitefarm_core\BlockConfigFormHelpers $blockConfigFormHelpers
   */
  public function __construct(BlockConfigFormHelpers $blockConfigFormHelpers) {
    $this->blockConfigFormHelpers = $blockConfigFormHelpers;
  }

  /**
   * Add javascript to check the url path and populate the path visibility with
   * the current page if using the Place Block module
   *
   * @param $form
   * @param $form_id
   */
  public function attachPlaceBlockVisibilityJs(&$form, $form_id) {
    if ($form_id == 'block_form') {
      $form['#attached']['library'][] = 'sitefarm_core/sitefarm_core.blocks_path_visibility';
    }
  }

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
