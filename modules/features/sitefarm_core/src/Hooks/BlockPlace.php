<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Url;

/**
 * Class BlockPlace.
 *
 * Helper utility for the Block Place module to help enhance usability in hooks.
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class BlockPlace {

  /**
   * The path matcher service.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * BlockPlace constructor.
   *
   * @param \Drupal\Core\Path\PathMatcher $pathMatcher
   */
  public function __construct(PathMatcher $pathMatcher) {
    $this->pathMatcher = $pathMatcher;
  }

  /**
   * Add javascript to check the url path and populate the path visibility with
   * the current page if using the Place Block module
   *
   * @param $form
   * @param $form_id
   */
  public function attachBlockVisibilityJs(&$form, $form_id) {
    if ($form_id == 'block_form') {
      $form['#attached']['library'][] = 'sitefarm_core/sitefarm_core.blocks_path_visibility';
    }
  }

  /**
   * Fix the url of the Place Block link on the front page so that it works
   *
   * @param array $items
   */
  public function fixFrontPageLink(array &$items) {
    if (isset($items['block_place'])) {
      $options = $items['block_place']['tab']['#url']->getOptions();

      $is_front = $this->pathMatcher->isFrontPage();

      if ($is_front) {
        $new_url = Url::fromRoute('<front>', [], $options);
        $items['block_place']['tab']['#url'] = $new_url;
      }
    }
  }

}
