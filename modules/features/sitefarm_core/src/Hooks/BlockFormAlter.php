<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class BlockFormAlter.
 *
 * Helper utility to break out block form alter procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class BlockFormAlter {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  protected $configFactory;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config Factory service.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
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
      $hide = $this->configFactory
        ->get('sitefarm_core.settings')
        ->get('block_visibility_hidden');
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

  public function removeViewsContextualElements(&$form, $form_id) {
    if ($form_id == 'block_form' && !empty($form['settings']['context_mapping'])) {
      $context_keys = $this->configFactory
        ->get('sitefarm_core.settings')
        ->get('block_views_contextual_hidden');

      foreach ($context_keys as $key) {
        if (array_key_exists($key, $form['settings']['context_mapping'])) {
          $form['settings']['context_mapping'][$key]['#access'] = FALSE;
        }
      }
    }
  }

}
