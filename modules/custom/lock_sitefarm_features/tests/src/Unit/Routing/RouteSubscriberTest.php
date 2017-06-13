<?php

namespace Drupal\Tests\lock_sitefarm_features\Unit\Routing;

use Drupal\Tests\UnitTestCase;
use Drupal\lock_sitefarm_features\Routing\RouteSubscriber;
use Drupal\Core\Routing\RouteBuildEvent;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\lock_sitefarm_features\Routing\RouteSubscriber
 * @group lock_sitefarm_features
 */
class RouteSubscriberTest extends UnitTestCase {

  /**
   * Tests getSubscribedEvents()
   */
  public function testGetSubscribedEvents() {
    $expected = [
      'routing.route_alter' => [
        'onAlterRoutes',
        -9999
      ]
    ];

    $subscriber = new RouteSubscriber();

    $return = $subscriber->getSubscribedEvents();
    $this->assertArrayEquals($expected, $return);
  }

  /**
   * Tests alterRoutes()
   */
  public function testAlterRoutes() {
    $page = 'entity.entity_form_display.node.default';

    $route = new Route($page);

    $collection = $this->prophesize(RouteCollection::CLASS);
    $collection->get(Argument::any())->willReturn(NULL);
    $collection->get($page)->willReturn($route);

    $event = $this->prophesize(RouteBuildEvent::CLASS);
    $event->getRouteCollection()->willReturn($collection->reveal());

    $subscriber = new RouteSubscriber();
    $subscriber->onAlterRoutes($event->reveal());
    $subscriber->getSubscribedEvents();

    $requirements = $route->getRequirements();
    $expected = ['_lock_sitefarm_features' => 'TRUE'];

    $this->assertArrayEquals($expected, $requirements);
  }

}
