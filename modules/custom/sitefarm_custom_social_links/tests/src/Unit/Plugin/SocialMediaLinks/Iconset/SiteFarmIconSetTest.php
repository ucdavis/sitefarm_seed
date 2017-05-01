<?php

namespace Drupal\Tests\sitefarm_custom_social_links\Unit\Plugin\SocialMediaLinks\Iconset;

use Drupal\Tests\UnitTestCase;
use Drupal\social_media_links\IconsetFinderService;

/**
 * @coversDefaultClass \Drupal\sitefarm_custom_social_links\Plugin\SocialMediaLinks\Iconset\SiteFarmIconSet
 * @group sitefarm_custom_social_links
 */
class SiteFarmIconSetTest extends UnitTestCase {

  /**
   * Prophecy stub of the IconsetFinderService
   *
   * @var \Drupal\social_media_links\IconsetFinderService
   */
  protected $iconsetFinderService;

  /**
   * @var \Drupal\sitefarm_custom_social_links\Plugin\SocialMediaLinks\Iconset\SiteFarmIconSet
   */
  protected $iconSet;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUp();

    // stub the Iconset Finder Service
    $this->iconsetFinderService = $this->prophesize(IconsetFinderService::CLASS);

    $configuration = [];
    $plugin_id = 'sitefarm_custom_social_links';
    $plugin_definition['provider'] = 'sitefarm_custom_social_links';

    $this->iconSet = new TestSiteFarmIconSet(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->iconsetFinderService->reveal()
    );
  }

  /**
   * Tests the getStyle method.
   *
   * @see ::getStyle()
   */
  public function testGetStyle() {
    $expected = [
      'default' => 'default',
    ];

    $this->assertArrayEquals($expected, $this->iconSet->getStyle());
  }

  /**
   * Tests the getIconPath method.
   *
   * @see ::getIconPath()
   */
  public function testGetIconPath() {
    $expected = 'path/img/test.svg';

    $this->assertEquals($expected, $this->iconSet->getIconPath('test', ''));
  }
}