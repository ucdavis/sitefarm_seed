<?php

namespace Drupal\Tests\ck_media_link\Unit\Plugin\Filter;

use Drupal\Tests\UnitTestCase;
use Drupal\ck_media_link\Plugin\Filter\FilterMediaLink;
use Drupal\Core\Path\PathValidator;
use Drupal\Core\Path\AliasManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\filter\FilterProcessResult;

/**
 * @coversDefaultClass \Drupal\ck_media_link\Plugin\Filter\FilterMediaLink
 * @group ck_media_link
 */
class FilterMediaLinkTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Path\PathValidator $pathValidator
   */
  protected $pathValidator;

  /**
   * @var \Drupal\Core\Path\AliasManager $aliasManager
   */
  protected $aliasManager;

  /**
   * @var \Drupal\ck_media_link\Plugin\Filter\FilterMediaLink
   */
  protected $plugin;

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp()
  {
    parent::setUp();

    // stub path validator
    $this->pathValidator = $this->prophesize(PathValidator::CLASS);
    $this->pathValidator->isValid('http://link.test')->willReturn(TRUE);

    // stub alias manager
    $this->aliasManager = $this->prophesize(AliasManager::CLASS);
    $this->aliasManager->getAliasByPath('http://link.test')->willReturn('http://link.test');


    $configuration = [];
    $plugin_id = 'ck_media_link';
    $plugin_definition['provider'] = 'ck_media_link';

    $this->plugin = new FilterMediaLink(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->pathValidator->reveal(),
      $this->aliasManager->reveal()
    );
  }

  /**
   * Tests the create method.
   *
   * @see ::create()
   */
  public function testCreate() {
    $configuration = [];
    $plugin_id = 'ck_media_link';
    $plugin_definition['provider'] = 'ck_media_link';

    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('path.validator')->willReturn($this->pathValidator);
    $container->get('path.alias_manager')->willReturn($this->aliasManager);

    $instance = FilterMediaLink::create($container->reveal(), $configuration, $plugin_id, $plugin_definition);
    $this->assertInstanceOf(FilterMediaLink::CLASS, $instance);
  }

  /**
   * Tests the process method.
   *
   * @see ::process()
   */
  public function testProcess() {
    $text = <<<EOL
<div class="media-link__wrapper" data-url="http://link.test">
  <div class="media-link__figure"><img alt="" data-entity-type="file" data-entity-uuid="placeholder" src="http://placehold.it/135x135" /></div>
  <div class="media-link__body">
    <h3 class="media-link__title">Title</h3>
    <div class="media-link__content">
      <p>Content</p>
    </div>
  </div>
</div>
EOL;


    $expected = <<<EOL
<a href="http://link.test" class="media-link"><div class="media-link__wrapper" data-url="http://link.test">
  <div class="media-link__figure"><img alt="" data-entity-type="file" data-entity-uuid="placeholder" src="http://placehold.it/135x135" /></div>
  <div class="media-link__body">
    <h3 class="media-link__title">Title</h3>
    <div class="media-link__content">
      <p>Content</p>
    </div>
  </div>
</div></a>
EOL;

    $return = $this->plugin->process($text, 'en');

    $this->assertInstanceOf(FilterProcessResult::CLASS, $return);
    $this->assertEquals($expected, $return->getProcessedText());
  }

  /**
   * Tests the formatPath method.
   *
   * @see ::formatPath()
   */
  public function testFormatPath() {
    // Path is valid
    $expected = 'http://link.test';
    $path = 'http://link.test';

    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);

    // Path is HTTPS
    $expected = 'https://link.test';
    $path = 'https://link.test';

    $this->pathValidator->isValid($path)->willReturn(TRUE);
    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);

    // Path is internal
    $expected = '/link-test';
    $path = '/link-test';

    $this->pathValidator->isValid($path)->willReturn(TRUE);
    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);

    // Path is relative and valid
    $expected = '/link-test';
    $path = 'link-test';

    $this->pathValidator->isValid($path)->willReturn(TRUE);
    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);

    // Path is relative and invalid internal link (ex www.google.com)
    $expected = 'http://www.invalid-internal';
    $path = 'www.invalid-internal';

    $this->pathValidator->isValid('/' . $path)->willReturn(FALSE);
    $this->aliasManager->getAliasByPath('/' . $path)->willReturn('/' . $path);
    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);

    // Path is relative invalid but is valid internal alias
    $expected = '/valid-alias';
    $path = '/valid-alias';

    $this->pathValidator->isValid($path)->willReturn(FALSE);
    $this->aliasManager->getAliasByPath($path)->willReturn('node/1');
    $return = $this->plugin->formatPath($path);

    $this->assertEquals($expected, $return);
  }
}
