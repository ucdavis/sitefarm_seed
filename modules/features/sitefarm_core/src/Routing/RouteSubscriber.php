<?php

namespace Drupal\sitefarm_core\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\sitefarm_core\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Block Content Type admin routes needing access restrictions
    $restricted_pages = [
      'entity.block_content_type.collection',
      'block_content.type_add',
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
    ];

    // Hide and restrict access to the follow pages without the proper permission
    foreach ($restricted_pages as $page) {
      if ($route = $collection->get($page)) {
        $route->setRequirement('_permission', 'administer block content types');
      }
    }
  }
}
