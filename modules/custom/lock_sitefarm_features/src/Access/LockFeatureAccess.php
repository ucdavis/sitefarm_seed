<?php

namespace Drupal\lock_sitefarm_features\Access;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Extension\ThemeHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Checks access for displaying configuration translation page.
 */
class LockFeatureAccess implements AccessInterface {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  protected $configFactory;

  /**
   * @var \Drupal\Core\Extension\ThemeHandler $themeHandler
   */
  protected $themeHandler;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface $route_match
   */
  protected $routeMatch;

  /**
   * @var \Drupal\Core\Session\AccountInterface $account
   */
  protected $account;

  /**
   * LockFeatureAccess constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config Factory service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The parametrized route
   * @param \Drupal\Core\Extension\ThemeHandler $theme_handler
   */
  public function __construct(ConfigFactoryInterface $configFactory, RouteMatchInterface $route_match, ThemeHandler $theme_handler) {
    $this->configFactory = $configFactory;
    $this->routeMatch = $route_match;
    $this->themeHandler = $theme_handler;

    // set properties
    $this->getLockedNodeTypes();
    $this->getLockedBlockTypes();
    $this->getLockedTextFormats();
    $this->getLockedTaxonomy();
    $this->getLockedPathautoPatterns();
    $this->getLockedImageStyles();
    $this->getLockedViews();
  }

  /**
   * Content Types which should be restricted
   *
   * @var array
   */
  protected $lockedNodeTypes = [];

  /**
   * Block Types which should be restricted
   *
   * @var array
   */
  protected $lockedBlockTypes = [];

  /**
   * Text Formats which should be restricted
   *
   * @var array
   */
  protected $lockedTextFormats = [];

  /**
   * Taxonomy Vocabularies which should be restricted
   *
   * @var array
   */
  protected $lockedTaxonomy = [];

  /**
   * Pathauto Patterns which should be restricted
   *
   * @var array
   */
  protected $lockedPathautoPatterns = [];

  /**
   * Image Styles which should be restricted
   *
   * @var array
   */
  protected $lockedImageStyles = [];

  /**
   * Views which should be restricted
   *
   * @var array
   */
  protected $lockedViews = [];

  /**
   * A custom access check.
   *
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @return AccessResult
   */
  public function access(AccountInterface $account) {
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
    $this->lockedNodeTypes = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_node_types');

    return $this->lockedNodeTypes;
  }

  /**
   * @return array
   */
  public function getLockedBlockTypes() {
    $this->lockedBlockTypes = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_block_content_types');

    return $this->lockedBlockTypes;
  }

  /**
   * @return array
   */
  public function getLockedTextFormats() {
    $this->lockedTextFormats = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_filter_formats');

    return $this->lockedTextFormats;
  }

  /**
   * @return array
   */
  public function getLockedTaxonomy() {
    $this->lockedTaxonomy = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_taxonomy_vocabularies');

    return $this->lockedTaxonomy;
  }

  /**
   * @return array
   */
  public function getLockedPathautoPatterns() {
    $this->lockedPathautoPatterns = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_pathauto_patterns');

    return $this->lockedPathautoPatterns;
  }

  /**
   * @return array
   */
  public function getLockedImageStyles() {
    $this->lockedImageStyles = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_image_styles');

    return $this->lockedImageStyles;
  }

  /**
   * @return array
   */
  public function getLockedViews() {
    $this->lockedViews = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_views');

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
      $locked_themes = $this->configFactory
        ->get('lock_sitefarm_features.settings')
        ->get('locked_themes');

      $theme = $this->themeHandler->getDefault();
      if (in_array($theme, $locked_themes)) {
        $restricted = $this->isLockedEntity('image_style', 'lockedImageStyles');
      }
    }

    // Field Storage (if not already restricted by something else)
    if (!$restricted && $this->routeMatch->getParameter('field_config')) {
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
    // Get the entity type
    $entity_type = $this->routeMatch->getRawParameter($parameter);

    if ($this->matchesLockedPattern($entity_type) || in_array($entity_type, $this->$property)) {
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

      // Get field config argument
      $field = $this->routeMatch->getParameter('field_config');

      $patterns = $this->configFactory
        ->get('lock_sitefarm_features.settings')
        ->get('locked_prefix_patterns');

      foreach ($patterns as $pattern) {
        // expression to match a field storage config - node.test.field_sf_primary_image
        $regex = '/field_' . $pattern . '/';

        if (preg_match($regex, $field)) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * Determine if a value passed in matches prefix patterns which should be
   * locked.
   *
   * @param $entity_name
   * @return bool
   */
  public function matchesLockedPattern($entity_name) {
    $patterns = $this->configFactory
      ->get('lock_sitefarm_features.settings')
      ->get('locked_prefix_patterns');

    foreach ($patterns as $pattern) {
      if (preg_match('/^' . $pattern . '/', $entity_name)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
