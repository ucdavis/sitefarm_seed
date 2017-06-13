<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\Core\Database\Query\Condition;

/**
 * Class ViewsAlter.
 *
 * Helper utility to break out views alter procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class ViewsAlter {

  /**
   * Hide users who have the administrator role on admin "people" display
   *
   * @param ViewExecutable $view
   * @param QueryPluginBase $query
   */
  public function hideAdministratorsOnPeopleDisplay(ViewExecutable $view, QueryPluginBase $query) {
    if ($view->id() == 'user_admin_people' && roleassign_restrict_access()) {
      // Create a Condition object so that we can check if a role is set or not
      // even available. Regular authenticated users do not get a user role.
      $or_condition = new Condition('OR');

      $query->addTable('user__roles');
      $query->addWhere(0, $or_condition
        ->condition('user__roles.roles_target_id', 'administrator', '<>')
        ->condition('user__roles.roles_target_id', 'administrator', 'IS NULL')
      );
      $query->distinct = TRUE;
    }
  }
}
