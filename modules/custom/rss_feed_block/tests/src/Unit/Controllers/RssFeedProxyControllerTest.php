<?php

namespace Drupal\Tests\rss_feed_block\Unit\Controllers;

use Drupal\Tests\UnitTestCase;
use Drupal\rss_feed_block\Controllers\RssFeedProxyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\block\Entity\Block;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Exception\RequestException;


/**
 * @coversDefaultClass \Drupal\rss_feed_block\Controllers\RssFeedProxyController
 * @group rss_feed_block
 */
class RssFeedProxyControllerTest extends UnitTestCase {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\rss_feed_block\Controllers\RssFeedProxyController
   */
  protected $controller;

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp()
  {
    parent::setUp();

    // Mock the Guzzle client
    $guzzleResponse = $this->prophesize(ResponseInterface::CLASS);
    $guzzleResponse->getBody()->willReturn('<entry>test</entry>');

    $this->client = $this->prophesize(Client::CLASS);
    $this->client->get('http://test.url')->willReturn($guzzleResponse->reveal());

    // Stub the Entity Type Manager
    $this->entityTypeManager = $this->prophesize(EntityTypeManagerInterface::CLASS);

    $this->controller = new RssFeedProxyController(
      $this->client->reveal(),
      $this->entityTypeManager->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->controller->setStringTranslation($translator);
  }

  /**
   * Tests create().
   */
  public function testCreate() {
    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('http_client')->willReturn($this->client);
    $container->get('entity_type.manager')->willReturn($this->entityTypeManager);

    $instance = RssFeedProxyController::create($container->reveal());
    $this->assertInstanceOf('Drupal\rss_feed_block\Controllers\RssFeedProxyController', $instance);
  }

  /**
   * Tests getFeed().
   */
  public function testGetFeed() {
    // Create the Request object parameters mock
    $params = $this->prophesize(ParameterBag::CLASS);
    $params->get('id')->willReturn('block_id');

    $request = $this->prophesize(Request::CLASS);
    $request->request = $params->reveal();

    $block = $this->prophesize(Block::CLASS);
    $block->get('settings')->willReturn(['rss_url' => 'http://test.url']);

    $entityStorage = $this->prophesize(EntityStorageInterface::CLASS);
    $entityStorage->load('block_id')->willReturn($block->reveal());

    $this->entityTypeManager->getStorage('block')->willReturn($entityStorage->reveal());

    // A successful response
    $response = $this->controller->getFeed($request->reveal());
    $this->assertEquals('<item>test</item>', $response->getContent());

    // An improper block ID is used to fetch the feed
    $entityStorage->load('block_id')->willReturn(FALSE);
    $response = $this->controller->getFeed($request->reveal());
    $this->assertTrue($response->isClientError());

    // Throw an exception
    $exceptionResponse = $this->prophesize(ResponseInterface::CLASS);
    $exceptionResponse->getStatusCode()->willReturn(404);
    $exceptionResponse->getReasonPhrase()->willReturn('It Failed');

    $exception = $this->prophesize(RequestException::CLASS);
    $exception->getResponse()->willReturn($exceptionResponse->reveal());

    $this->client->get('error')->willThrow($exception->reveal());
    $entityStorage->load('block_id')->willReturn($block->reveal());
    $block->get('settings')->willReturn(['rss_url' => 'error']);

    $response = $this->controller->getFeed($request->reveal());
    $this->assertTrue($response->isServerError());
    $this->assertEquals(503, $response->getStatusCode());
  }

}
