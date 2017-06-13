<?php
namespace Drupal\Tests\rss_feed_block\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\rss_feed_block\Plugin\Block\RssFeedBlock;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rss_feed_block\Plugin\Block;

/**
 * @coversDefaultClass \Drupal\rss_feed_block\Plugin\Block\RssFeedBlock
 * @group rss_feed_block
 */
class RssFeedBlockTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\rss_feed_block\Plugin\Block\RssFeedBlock
   */
  protected $plugin;

  /**
   * Default config for the block plugin
   *
   * @var array
   */
  protected $pluginConfig = [
    'rss_url' => FALSE,
    'items_count' => 5,
    'feed_text' => FALSE,
    'text_cutoff' => FALSE,
    'more_button' => TRUE,
  ];

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp()
  {
    parent::setUp();

    $this->formState = $this->getMock(FormStateInterface::CLASS);

    // stub email validator
    $configuration = [];
    $plugin_id = 'rss_feed_block';
    $plugin_definition['provider'] = 'rss_feed_block';

    $this->plugin = new RssFeedBlock(
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
    // Default config
    $form = [];
    $return = $this->plugin->blockForm($form, $this->formState);

    $this->assertFalse($return['display']['rss_url']['#default_value']);
    $this->assertEquals(5, $return['display']['items_count']['#default_value']);
    $this->assertFalse($return['display']['feed_text']['#default_value']);
    $this->assertFalse($return['display']['text_cutoff']['#default_value']);
    $this->assertTrue($return['display']['more_button']['#default_value']);

    // Altered config
    $this->plugin->setConfiguration([
      'rss_url' => 'test_url',
      'items_count' => 2,
      'feed_text' => 'paragraph',
      'text_cutoff' => '500',
      'more_button' => FALSE,
    ]);
    $return = $this->plugin->blockForm($form, $this->formState);

    $this->assertEquals('test_url', $return['display']['rss_url']['#default_value']);
    $this->assertEquals(2, $return['display']['items_count']['#default_value']);
    $this->assertEquals('paragraph', $return['display']['feed_text']['#default_value']);
    $this->assertEquals('500', $return['display']['text_cutoff']['#default_value']);
    $this->assertFalse($return['display']['more_button']['#default_value']);
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
        'id' => 'rss_feed_block',
        'label' => '',
        'provider' => 'rss_feed_block',
        'label_display' => 'visible',
      );

    $form = [];
    $this->plugin->blockSubmit($form, $this->formState);
    $this->assertArrayEquals($expectedConfig, $this->plugin->getConfiguration());
  }

  /**
   * Tests build()
   */
  public function testBuildHasUrlAndRatioReturned() {
    $expected = [
      'items_count' => 5,
      'feed_text' => FALSE,
      'text_cutoff' => FALSE,
      'more_button' => TRUE,
      '#attached' => array(
        'library' =>  array(
          'rss_feed_block/rss_feed_block'
        ),
      ),
    ];

    $return = $this->plugin->build();

    $this->assertArrayEquals($expected, $return);
  }
}
