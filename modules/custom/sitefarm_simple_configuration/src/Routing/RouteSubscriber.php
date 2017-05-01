<?php

namespace Drupal\sitefarm_simple_configuration\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\sitefarm_simple_configuration\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Construction method.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Pages which should be restricted from all but Admins
    $restricted_pages = $this->configFactory->get('sitefarm_simple_configuration.settings')->get('restricted_routes');

    // Hide and restrict access to the follow pages without the proper permission
    foreach ($restricted_pages as $page) {
      if ($route = $collection->get($page)) {
        $route->setRequirement('_permission', 'administer core configuration');
      }
    }
  }
}
