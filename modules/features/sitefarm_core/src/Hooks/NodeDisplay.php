<?php

namespace Drupal\sitefarm_core\Hooks;

/**
 * Class NodeDisplay.
 *
 * Helper utility to break out node display procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class NodeDisplay {

  /**
   * Use the Restricted text format for teasers on a body field
   *
   * @param array $build
   * @param $view_mode
   */
  public function forceRestrictedHtmlOnTeasers(array &$build, $view_mode) {
    if ($view_mode == 'teaser'
      && isset($build['body']['#formatter'])
      && $build['body']['#formatter'] == 'text_summary_or_trimmed'
    ) {
      $build['body'][0]['#format'] = 'sf_restricted_html';
    }
  }

  /**
   * Use the Plain text format on Poster view modes of a body field
   *
   * @param array $build
   * @param $view_mode
   */
  public function forcePlainTextOnPoster(array &$build, $view_mode) {
    // Strip Poster view mode html and use plain text filter
    if ($view_mode == 'poster' && isset($build['body'][0]['#format'])) {
      $build['body'][0]['#text'] = strip_tags($build['body'][0]['#text']);
      $build['body'][0]['#format'] = 'sf_plain_text';
    }
  }

  /**
   * Add featured status to nodes for Teaser view mode
   * @param array $variables
   */
  public function addFeaturedStatus(array &$variables) {
    $node = $variables['node'];

    // Pass a Featured Status to Teasers
    if ($variables['view_mode'] == 'teaser' && $node->field_sf_featured_status) {
      $variables['featured_status'] = $node->field_sf_featured_status->value;
    }
  }
}
