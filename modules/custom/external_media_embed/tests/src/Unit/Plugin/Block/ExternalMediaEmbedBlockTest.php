<?php

namespace Drupal\Tests\external_media_embed\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\external_media_embed\Plugin\Block\ExternalMediaEmbedBlock;
use Drupal\Core\Form\FormStateInterface;
use Drupal\url_embed\UrlEmbedInterface;
use Embed\Adapters\AdapterInterface;

/**
 * @coversDefaultClass \Drupal\external_media_embed\Plugin\Block\ExternalMediaEmbedBlock
 * @group external_media_embed
 */
class ExternalMediaEmbedBlockTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Embed\Adapters\AdapterInterface
   */
  protected $adapter;

  /**
   * @var \Drupal\url_embed\UrlEmbedInterface
   */
  protected $urlEmbed;

  /**
   * @var \Drupal\external_media_embed\Plugin\Block\ExternalMediaEmbedBlock
   */
  protected $plugin;

  /**
   * Default config for the block plugin
   *
   * @var array
   */
  protected $pluginConfig = [
    'embed_url' => FALSE,
    'label_display' => FALSE,
  ];

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp()
  {
    parent::setUp();

    $this->formState = $this->getMock(FormStateInterface::CLASS);

    $this->adapter = $this->prophesize(AdapterInterface::CLASS);
    $this->adapter->getCode()->willReturn('output_url_markup');

    $this->urlEmbed = $this->prophesize(UrlEmbedInterface::CLASS);
    $this->urlEmbed->getEmbed(FALSE)->willReturn(FALSE);

    // stub email validator
    $configuration = [];
    $plugin_id = 'external_media_embed';
    $plugin_definition['provider'] = 'external_media_embed';

    $this->plugin = new ExternalMediaEmbedBlock(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests defaultConfiguration()
   */
  public function testDefaultConfiguration() {
    $expected = $this->pluginConfig;

    $this->assertArrayEquals($expected, $this->plugin->defaultConfiguration());
  }

  /**
   * Tests blockForm()
   */
  public function testBlockForm() {
    $this->plugin->setConfigurationValue('embed_url', 'test_url');

    $form = [];
    $return = $this->plugin->blockForm($form, $this->formState);

    $this->assertFalse($return['display']['#collapsed']);
    $this->assertEquals('test_url', $return['display']['embed_url']['#default_value']);
  }

  /**
   * Tests blockSubmit()
   */
  public function testBlockSubmit() {
    $options = array(
      'display' => $this->pluginConfig
    );

    $this->formState->expects($this->any())
      ->method('getValues')
      ->willReturn($options);
    $this->formState->expects($this->any())
      ->method('getValue')
      ->willReturnCallback(function ($array) use ($options) {
        return $options[$array[0]][$array[1]];
      });

    $expectedConfig = $this->pluginConfig + array(
        'id' => 'external_media_embed',
        'label' => '',
        'provider' => 'external_media_embed',
        'label_display' => FALSE,
      );

    $form = [];
    $this->plugin->blockSubmit($form, $this->formState);
    $this->assertArrayEquals($expectedConfig, $this->plugin->getConfiguration());
  }

  /**
   * No url embed address is passed in the config
   *
   * @covers ::build()
   */
  public function testBuildHasNoAddress() {
    $this->plugin->setUrlEmbed($this->urlEmbed->reveal());

    $this->assertEmpty($this->plugin->build());
  }

  /**
   * A url IS passed through config and has not aspect ratio returned
   *
   * @covers ::build()
   */
  public function testBuildHasUrlAndNoRatioReturned() {
    $this->plugin->setConfigurationValue('embed_url', 'http://test.url');

    $this->adapter->aspectRatio = FALSE;

    $this->urlEmbed->getEmbed('http://test.url')->willReturn($this->adapter->reveal());
    $this->plugin->setUrlEmbed($this->urlEmbed->reveal());

    $return = $this->plugin->build();

    $this->assertArrayEquals(['#markup' => 'output_url_markup'], $this->plugin->build());
    $this->assertInstanceOf('Drupal\Component\Render\MarkupInterface', $return['#markup']);
  }

  /**
   * A url IS passed through config and DOES have an aspect ratio returned
   *
   * @covers ::build()
   */
  public function testBuildHasUrlAndRatioReturned() {
    $this->plugin->setConfigurationValue('embed_url', 'http://test.url');

    $this->adapter->aspectRatio = 'aspect ratio test';

    $this->urlEmbed->getEmbed('http://test.url')->willReturn($this->adapter->reveal());
    $this->plugin->setUrlEmbed($this->urlEmbed->reveal());

    $return = $this->plugin->build();

    $this->assertEquals('responsive_embed', $return['#theme']);
    $this->assertEquals('aspect ratio test', $return['#ratio']);
    $this->assertEquals('output_url_markup', $return['#url_output']);
  }

  /**
   * The build throws an exception due to the embed info not executing
   *
   * @covers ::build()
   */
  public function testBuildThrowsException() {
    $this->setExpectedException(\Throwable::class);
    $this->plugin->build();
  }
}
