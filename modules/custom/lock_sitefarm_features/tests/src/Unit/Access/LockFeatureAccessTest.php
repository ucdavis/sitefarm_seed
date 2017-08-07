<?php

namespace Drupal\Tests\lock_sitefarm_features\Unit\Access;

use Drupal\Tests\UnitTestCase;
use Drupal\lock_sitefarm_features\Access\LockFeatureAccess;
use Drupal\Core\Extension\ThemeHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\lock_sitefarm_features\Access\LockFeatureAccess
 * @group lock_sitefarm_features
 */
class LockFeatureAccessTest extends UnitTestCase {

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * @var \Drupal\lock_sitefarm_features\Access\LockFeatureAccess $access
   */
  protected $access;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   */
  protected $routeMatch;

  /**
   * @var \Drupal\Core\Session\AccountInterface $account
   */
  protected $account;

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp() {
    parent::setUp();

    $parameters = [
      'node_type' => 'sf_page',
      'block_content_type' => 'sf_basic',
      'filter_format' => 'sf_basic_html',
      'pathauto_pattern' => 'sf_page',
      'taxonomy_vocabulary' => 'sf_tags',
      'view' => 'sf_articles_recent',
    ];

    // Stub config
    $this->configFactory = $this->getConfigFactoryStub([
      'lock_sitefarm_features.settings' => [
        'locked_prefix_patterns' => [
          'sf_',
          'test_',
        ],
        'locked_node_types' => [
          'sf_page',
        ],
        'locked_block_content_types' => [
          'sf_basic',
        ],
        'locked_filter_formats' => [
          'sf_basic_html',
        ],
        'locked_pathauto_patterns' => [
          'sf_page',
        ],
        'locked_taxonomy_vocabularies' => [
          'sf_tags',
        ],
        'locked_views' => [
          'sf_articles_recent',
        ],
        'locked_image_styles' => [
          'sf_thumbnail',
        ],
        'locked_themes' => [
          'sitefarm_theme',
        ],
      ],
    ]);

    $this->routeMatch = $this->prophesize(RouteMatchInterface::CLASS);
    $this->routeMatch->getParameter(Argument::any())->willReturn(FALSE);

    foreach ($parameters as $parameter => $bundle) {
      $this->routeMatch->getRawParameter($parameter)->willReturn($bundle);
    }

    $this->account = $this->prophesize(AccountInterface::CLASS);
    $this->account->id()->willReturn(2);

    $theme_handler = $this->prophesize(ThemeHandler::CLASS);
    $theme_handler->getDefault()->willReturn('sitefarm_theme');

    $this->access = new LockFeatureAccess(
      $this->configFactory,
      $this->routeMatch->reveal(),
      $theme_handler->reveal()
    );
  }

  /**
   * Tests getLockedNodeTypes()
   */
  public function testGetLockedNodeTypes() {
    $return = $this->access->getLockedNodeTypes();
    $this->assertContains('sf_page', $return);
  }

  /**
   * Tests getLockedBlockTypes()
   */
  public function testGetLockedBlockTypes() {
    $return = $this->access->getLockedBlockTypes();
    $this->assertContains('sf_basic', $return);
  }

  /**
   * Tests getLockedTextFormats()
   */
  public function testGetLockedTextFormats() {
    $return = $this->access->getLockedTextFormats();
    $this->assertContains('sf_basic_html', $return);
  }

  /**
   * Tests getLockedTaxonomy()
   */
  public function testGetLockedTaxonomy() {
    $return = $this->access->getLockedTaxonomy();
    $this->assertContains('sf_tags', $return);
  }

  /**
   * Tests getLockedPathautoPatterns()
   */
  public function testGetLockedPathautoPatterns() {
    $return = $this->access->getLockedPathautoPatterns();
    $this->assertContains('sf_page', $return);
  }

  /**
   * Tests getLockedImageStyles()
   */
  public function testGetLockedImageStyles() {
    $return = $this->access->getLockedImageStyles();
    $this->assertContains('sf_thumbnail', $return);
  }

  /**
   * Tests getLockedViews()
   */
  public function testGetLockedViews() {
    $return = $this->access->getLockedViews();
    $this->assertContains('sf_articles_recent', $return);
  }

  /**
   * Tests isLockedEntity()
   */
  public function testIsLockedEntity() {
    $return = $this->access->isLockedEntity('node_type', 'lockedNodeTypes');
    $this->assertTrue($return);

    // bundle not found in restricted list
    $this->routeMatch->getRawParameter('node_type')->willReturn('test');

    $return = $this->access->isLockedEntity('node_type', 'lockedNodeTypes');
    $this->assertFalse($return);
  }

  /**
   * Access allowed for Administrators
   *
   * Tests access()
   */
  public function testAccess() {
    $this->account->id()->willReturn(1);

    $return = $this->access->access($this->account->reveal());
    $this->assertTrue($return->isAllowed());
  }

  /**
   * Test that Non-administrators are denied access to restricted nodes
   */
  public function testAccessDeniedForNonAdministrator() {
    // restrict the a node route
    $this->routeMatch->getParameter('node_type')->willReturn(TRUE);

    $return = $this->access->access($this->account->reveal());
    $this->assertFalse($return->isAllowed());
  }

  /**
   * Test that Non-administrators are denied access to Image Styles
   */
  public function testAccessDeniedToImageStyle() {
    // restrict the image style route
    $this->routeMatch->getParameter('image_style')->willReturn(TRUE);
    $this->routeMatch->getRawParameter('image_style')->willReturn('sf_thumbnail');

    $return = $this->access->access($this->account->reveal());
    $this->assertFalse($return->isAllowed());
  }

  /**
   * Test access to Field Config
   */
  public function testAccessToFieldConfig() {
    // restrict the field config route
    $this->routeMatch->getRouteName()->willReturn('storage_edit_form');
    $this->routeMatch->getParameter('field_config')->willReturn('field_sf_test');

    $return = $this->access->access($this->account->reveal());
    $this->assertFalse($return->isAllowed());

    // Allow access to field config that doesn't have 'field_sf_' prefix
    $this->routeMatch->getParameter('field_config')->willReturn('field_test');

    $return = $this->access->access($this->account->reveal());
    $this->assertTrue($return->isAllowed());
  }

}
