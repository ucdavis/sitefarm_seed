<?php

namespace Drupal\Tests\site_credits\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\site_credits\Plugin\Block\SiteCreditsBlock;

class SiteCreditsBlockTest extends UnitTestCase
{

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * Form State stub.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * Default config for the block plugin
   *
   * @var array
   */
  protected $pluginConfig = array(
    'use_site_logo' => TRUE,
    'use_site_name' => TRUE,
    'use_site_slogan' => TRUE,
    'use_site_credit_info' => TRUE
  );

  /**
   * @var \Drupal\site_credits\Plugin\Block\SiteCreditsBlock
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUp();

    // Stub config
    $this->configFactory = $this->getConfigFactoryStub(array(
      'system.site' => array(
        'name' => 'testName',
        'slogan' => 'testSlogan',
        'credit_info' => [
          'value' => 'testValue',
          'format' => 'testFormat'
        ],
      ),
      'system.theme.global' => array(
        'logo.path' => 'theme_logo_url'
      ),
    ));

    // stub form_state
    $this->formState = $this->getMock('Drupal\Core\Form\FormStateInterface');

    $plugin_id = 'site_credits_block';
    $plugin_definition['provider'] = 'site_credits';

    $this->plugin = new TestSiteCreditsBlock($this->pluginConfig, $plugin_id, $plugin_definition, $this->configFactory);

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests the create method.
   *
   * @see \Drupal\site_credits\Plugin\Block\SiteCreditsBlock::create()
   */
  public function testCreate() {
    $plugin_id = 'site_credits_block';
    $plugin_definition['provider'] = 'site_credits';

    $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $container->expects($this->once())
      ->method('get')
      ->with('config.factory')
      ->willReturn($this->configFactory);

    $instance = SiteCreditsBlock::create($container, $this->pluginConfig, $plugin_id, $plugin_definition);
    $this->assertInstanceOf('Drupal\site_credits\Plugin\Block\SiteCreditsBlock', $instance);
  }

  /**
   * Tests the blockSubmit method.
   *
   * @see \Drupal\site_credits\Plugin\Block\SiteCreditsBlock::blockSubmit()
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
      'id' => 'site_credits_block',
      'label' => '',
      'provider' => 'site_credits',
      'label_display' => FALSE,
    );

    $form = array();
    $this->plugin->blockSubmit($form, $this->formState);
    $this->assertEquals($expectedConfig, $this->plugin->getConfiguration());
  }

  /**
   * Tests the build method.
   *
   * @see \Drupal\site_credits\Plugin\Block\SiteCreditsBlock::build()
   */
  public function testBuild() {
    $expectedBuild = array(
      'site_logo' => array(
        '#theme' => 'image',
        '#uri' => 'theme_logo_url',
        '#alt' => 'Home',
        '#access' => TRUE,
      ),
      'site_name' => array(
        '#markup' => 'testName',
        '#access' => TRUE,
      ),
      'site_slogan' => array(
        '#markup' => 'testSlogan',
        '#access' => TRUE,
      ),
      'site_credit_info' => array(
        '#access' => TRUE,
        '#type' => 'processed_text',
        '#text' => 'testValue',
        '#format' => 'testFormat',
      ),
    );

    $this->assertEquals($expectedBuild, $this->plugin->build());
  }

}
