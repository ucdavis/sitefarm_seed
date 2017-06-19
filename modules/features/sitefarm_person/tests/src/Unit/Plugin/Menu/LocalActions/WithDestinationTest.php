<?php

namespace Drupal\Tests\sitefarm_person\Unit\Plugin\Menu\LocalAction;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_person\Plugin\Menu\LocalAction\WithDestination;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * @coversDefaultClass \Drupal\sitefarm_person\Plugin\Menu\LocalAction\WithDestination
 * @group sitefarm_person
 */
class WithDestinationTest extends UnitTestCase
{

  /**
   * The route provider to load routes by name.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The redirect destination.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * @var \Drupal\sitefarm_person\Plugin\Menu\LocalAction\WithDestination
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp() {
    parent::setUp();

    // Stub the RouteProviderInterface
    $this->routeProvider = $this->prophesize(RouteProviderInterface::CLASS);

    // Stub the RedirectDestinationInterface
    $this->redirectDestination = $this->prophesize(RedirectDestinationInterface::CLASS);

    $config = [];
    $plugin_id = 'sitefarm_person_action';
    $plugin_definition['options'] = 'test_options';

    $this->plugin = new WithDestination(
      $config,
      $plugin_id,
      $plugin_definition,
      $this->routeProvider->reveal(),
      $this->redirectDestination->reveal()
    );
  }

  /**
   * Tests the create method.
   */
  public function testCreate() {
    $config = [];
    $plugin_id = 'sitefarm_person_action';
    $plugin_definition['provider'] = 'sitefarm_person';

    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('router.route_provider')->willReturn($this->routeProvider->reveal());
    $container->get('redirect.destination')->willReturn($this->redirectDestination->reveal());

    $instance = WithDestination::create($container->reveal(), $config, $plugin_id, $plugin_definition);
    $this->assertInstanceOf('Drupal\sitefarm_person\Plugin\Menu\LocalAction\WithDestination', $instance);
  }


  /**
   * Tests the getOptions method
   */
  public function testGetOptions() {
    $expected = [
      'test_options',
      'query' => [
        'destination' => 'dest'
      ],
    ];

    $this->redirectDestination->get()->willReturn('dest');

    $route_match = $this->prophesize(RouteMatchInterface::CLASS);

    $options = $this->plugin->getOptions($route_match->reveal());
    $this->assertArrayEquals($expected, $options);
  }

}
