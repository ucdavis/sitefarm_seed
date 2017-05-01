<?php

namespace Drupal\lock_sitefarm_features\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Checks access for displaying configuration translation page.
 */
class LockFeatureAccess implements AccessInterface {

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface $route_match
   */
  protected $routeMatch;

  /**
   * @var \Drupal\Core\Session\AccountInterface $account
   */
  protected $account;

  /**
   * Content Types which should be restricted
   *
   * @var array
   */
  protected $lockedNodeTypes = array(
    'sf_article',
    'sf_event',
    'sf_page',
    'sf_person',
    'sf_photo_gallery',
  );

  /**
   * Block Types which should be restricted
   *
   * @var array
   */
  protected $lockedBlockTypes = array(
    'sf_basic',
    'sf_focal_link',
    'sf_focus_box',
    'sf_hero_banner',
    'sf_marketing_highlight',
    'sf_marketing_highlight_horizntl',
  );

  /**
   * Text Formats which should be restricted
   *
   * @var array
   */
  protected $lockedTextFormats = array(
    'basic_html',
    'restricted_html',
    'full_html',
    'plain_text',
  );

  /**
   * Taxonomy Vocabularies which should be restricted
   *
   * @var array
   */
  protected $lockedTaxonomy = array(
    'sf_article_category',
    'sf_article_type',
    'sf_branding',
    'sf_event_type',
    'sf_person_type',
    'sf_photo_gallery_categories',
    'sf_tags',
  );

  /**
   * Pathauto Patterns which should be restricted
   *
   * @var array
   */
  protected $lockedPathautoPatterns = array(
    'sf_article',
    'sf_event',
    'sf_page',
    'sf_person',
    'sf_photo_gallery',
    'sf_article_category_terms',
    'default_taxonomy',
    'photo_gallery_alias_pattern',
  );

  /**
   * Image Styles which should be restricted
   *
   * @var array
   */
  protected $lockedImageStyles = array(
    'sf_focal_link',
    'sf_focus_box',
    'sf_gallery_full',
    'sf_gallery_thumbnail',
    'sf_hero_banner',
    'sf_landscape_4x3',
    'sf_landscape_16x9',
    'sf_medium_width',
    'sf_profile',
    'sf_slideshow_full',
    'sf_slideshow_thumbnail',
    'sf_small_width',
    'sf_thumbnail',
    'sf_title_banner',
    'sf_focal_point_cropped_landscape_16x9',
    'focal_point_thumbnail',
    'sf_large_width',
  );

  /**
   * Views which should be restricted
   *
   * @var array
   */
  protected $lockedViews = array(
    'sf_articles_category_filter',
    'sf_articles_latest_news',
    'sf_articles_recent',
    'sf_articles_related',
    'sf_events_category_filter',
    'sf_events_listing',
    'sf_events_upcoming',
    'sf_person_directory',
    'sf_persons_content_related_back_to_person',
    'sf_persons_related_to_content',
    'sf_photo_galleries_list',
  );

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The parametrized route
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @return AccessResult
   */
  public function access(RouteMatchInterface $route_match, AccountInterface $account) {
    $this->routeMatch = $route_match;
    $this->account = $account;

    // Default to restricting access
    $allowed = FALSE;

    if ($this->isUser1() || !$this->restrictedPath()) {
      $allowed = TRUE;
    }

    return AccessResult::allowedIf($allowed);
  }

  /**
   * @return array
   */
  public function getLockedNodeTypes() {
    return $this->lockedNodeTypes;
  }

  /**
   * @return array
   */
  public function getLockedBlockTypes() {
    return $this->lockedBlockTypes;
  }

  /**
   * @return array
   */
  public function getLockedTextFormats() {
    return $this->lockedTextFormats;
  }

  /**
   * @return array
   */
  public function getLockedTaxonomy() {
    return $this->lockedTaxonomy;
  }

  /**
   * @return array
   */
  public function getLockedPathautoPatterns() {
    return $this->lockedPathautoPatterns;
  }

  /**
   * @return array
   */
  public function getLockedImageStyles() {
    return $this->lockedImageStyles;
  }

  /**
   * @return array
   */
  public function getLockedViews() {
    return $this->lockedViews;
  }

  /**
   * Check if the current route path is restricted
   *
   * @return bool
   */
  public function restrictedPath() {
    $restricted = FALSE;

    // List of route parameter with the locked types property needed
    $parameters = [
      'node_type' => 'lockedNodeTypes',
      'block_content_type' => 'lockedBlockTypes',
      'filter_format' => 'lockedTextFormats',
      'pathauto_pattern' => 'lockedPathautoPatterns',
      'taxonomy_vocabulary' => 'lockedTaxonomy',
      'view' => 'lockedViews'
    ];

    // Loop through each Entity type via route parameter to see if we restrict
    foreach ($parameters as $parameter => $property) {
      if ($this->routeMatch->getParameter($parameter)) {
        $restricted = $this->isLockedEntity($parameter, $property);
      }
    }

    // Only restrict image styles if we are using the SiteFarm One theme
    if ($this->routeMatch->getParameter('image_style')) {
      $theme = \Drupal::service('theme_handler')->getDefault();
      if ($theme == 'sitefarm_one') {
        $restricted = $this->isLockedEntity('image_style', 'lockedImageStyles');
      }
    }

    // Field Storage (if not already restricted by something else)
    if ($this->routeMatch->getParameter('field_config') || !$restricted) {
      $restricted = $this->isLockedFieldStorage();
    }

    if ($restricted) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Check if the current entity viewed is a restricted SiteFarm type
   *
   * @return bool
   */
  public function isLockedEntity($parameter, $property) {
    // Get the node type
    $node_type = $this->routeMatch->getRawParameter($parameter);

    if (in_array($node_type, $this->$property)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check if the current user is User 1
   *
   * @return bool
   */
  public function isUser1() {
    $uid = $this->account->id();
    return ($uid == 1) ? TRUE : FALSE;
  }

  /**
   * Check that the current path is a SiteFarm field storage path
   *
   * @return bool
   */
  public function isLockedFieldStorage() {
    // Get the current route path
    $route = $this->routeMatch->getRouteName();

    if (strpos($route, 'storage_edit_form') !== FALSE) {
      // expression to match a field storage config - node.test.field_sf_primary_image
      $regex = '/field_sf_/';

      // Get field config argument
      $field = $this->routeMatch->getParameter('field_config');

      if (preg_match($regex, $field)) {
        return TRUE;
      }
    }

    return FALSE;
  }
}
