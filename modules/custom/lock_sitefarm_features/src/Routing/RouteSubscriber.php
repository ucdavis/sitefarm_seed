<?php

namespace Drupal\lock_sitefarm_features\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RoutingEvents;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\lock_sitefarm_features\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Pages which should be restricted from all but Admins
   *
   * @var array
   */
  protected $restrictedPages = array(
    // Nodes
    'entity.entity_form_display.node.default',
    'entity.entity_form_display.node.form_mode',
    'entity.entity_view_display.node.default',
    'entity.entity_view_display.node.view_mode',
    'entity.field_config.node_field_delete_form',
    'entity.field_config.node_field_edit_form',
    'entity.field_config.node_storage_edit_form',
    'entity.node.field_ui_fields',
    'entity.node_type.delete_form',
    'entity.node_type.edit_form',
    'entity.node_type.moderation',
    'entity.scheduled_update_type.add_form.field.node',
    'field_ui.field_storage_config_add_node',
    // Block Content Types
    'entity.entity_form_display.block_content.default',
    'entity.entity_form_display.block_content.form_mode',
    'entity.entity_view_display.block_content.default',
    'entity.entity_view_display.block_content.view_mode',
    'entity.field_config.block_content_field_delete_form',
    'entity.field_config.block_content_field_edit_form',
    'entity.field_config.block_content_storage_edit_form',
    'entity.block_content.field_ui_fields',
    'entity.block_content_type.delete_form',
    'entity.block_content_type.edit_form',
    'field_ui.field_storage_config_add_block_content',
    'entity.scheduled_update_type.add_form.field.block_content',
    // Text Filters
    'entity.filter_format.disable',
    'entity.filter_format.edit_form',
    'entity.filter_format.auto_label',
    // Image Styles
    'entity.image_style.delete_form',
    'entity.image_style.edit_form',
    'image.effect_add_form',
    'image.effect_delete',
    'image.effect_edit_form',
    // Taxonomy
    'entity.entity_form_display.taxonomy_term.default',
    'entity.entity_form_display.taxonomy_term.form_mode',
    'entity.entity_view_display.taxonomy_term.default',
    'entity.entity_view_display.taxonomy_term.view_mode',
    'entity.field_config.taxonomy_term_field_delete_form',
    'entity.field_config.taxonomy_term_field_edit_form',
    'entity.field_config.taxonomy_term_storage_edit_form',
    'entity.scheduled_update_type.add_form.field.taxonomy_term',
    'entity.taxonomy_term.field_ui_fields',
    'entity.taxonomy_vocabulary.delete_form',
    'entity.taxonomy_vocabulary.edit_form',
    'field_ui.field_storage_config_add_taxonomy_term',
    // Pathauto Patterns
    'entity.pathauto_pattern.delete_form',
    'entity.pathauto_pattern.disable',
    'entity.pathauto_pattern.edit_form',
    'entity.pathauto_pattern.enable',
    // Views
    'entity.view.delete_form',
    'entity.view.edit_display_form',
    'entity.view.edit_form',
  );

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Ensure that our route alterations occur last so that can not be overridden
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -9999];  // negative Values means "late"
    return $events;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Hide and restrict access to the follow pages without the proper permission
    foreach ($this->restrictedPages as $page) {
      if ($route = $collection->get($page)) {
        $route->setRequirement('_lock_sitefarm_features', 'TRUE');
      }
    }
  }
}
