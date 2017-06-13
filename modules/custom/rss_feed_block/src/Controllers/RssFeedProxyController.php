<?php

namespace Drupal\rss_feed_block\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * This is a proxy for fetching RSS Feeds due to not being able to fetch via
 * Ajax since it violates cross domain origin policy. This is thus a workaround.
 *
 * Returns responses for rss proxy routes.
 */
class RssFeedProxyController extends ControllerBase {
  use StringTranslationTrait;

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Instance of the Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * RssFeedProxyController constructor.
   * @param \GuzzleHttp\ClientInterface $client
   */
  public function __construct(ClientInterface $client, EntityTypeManagerInterface $entityTypeManager) {
    $this->client = $client;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Return an XML document to send to an Ajax request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The currently processing request.
   *
   * @return \Symfony\Component\HttpFoundation\Response $response
   *   An XML Response with the returned url's data.
   */
  public function getFeed(Request $request) {
    // Create a Response object
    $response = new Response();

    $url = $this->getUrlFromBlockId($request);

    // Exit if there is no URL
    if (!$url) {
      $response->setStatusCode(409);
      $response->setContent($this->t('This block ID does not have a URL available'));
      return $response;
    }

    try {
      // Fetch the data with Guzzle
      $guzzle = $this->client->get($url);
      $data = $guzzle->getBody();

      // Replace "entry" tags with "item" to standardize
      $data = str_replace(["<entry>","</entry>"], ["<item>","</item>"], $data);

      // Create the Response
      $response->setContent($data);
      $response->headers->set('Content-Type', 'xml');

    } catch (RequestException $e) {
      // Pass a 503 Service Unavailable due to an error getting the feed
      $response->setStatusCode(503);

      // Pass the actual feed response code and message for easier debugging
      $status_code = $e->getResponse()->getStatusCode();
      $status_message = $e->getResponse()->getReasonPhrase();
      $response->setContent($this->t('The actual status code returned by the RSS request was: @code @message',
        ['@code' => $status_code, '@message' => $status_message]));
    }

    return $response;
  }

  protected function getUrlFromBlockId(Request $request) {
    // Get the block id from $_POST['id']
    $block_id = $request->request->get('id');

    // Load the block instance based on the ID
    $block = $this->entityTypeManager->getStorage('block')->load($block_id);

    if ($block) {
      // Fetch the RSS URL that was set in the configuration
      return $block->get('settings')['rss_url'];
    }
    else {
      return FALSE;
    }
  }
}
