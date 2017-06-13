<?php

namespace Drupal\Tests\site_credits\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\site_credits\Plugin\Block\SiteCreditsBlock;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Access\AccessManagerInterface;

/**
 * @coversDefaultClass \Drupal\site_credits\Plugin\Block\SiteCreditsBlock
 * @group site_credits
 */
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

  /**
   * Tests blockForm().
   */
  public function testBlockForm() {
    // Set up the Service Container
    $access_manager = $this->prophesize(AccessManagerInterface::CLASS);
    $access_manager->checkNamedRoute("system.theme_settings_theme", ["theme" => null], null)->willReturn(TRUE);
    $access_manager->checkNamedRoute("system.site_information_settings", [], null)->willReturn(TRUE);
    $container = new ContainerBuilder();
    $container->set('access_manager', $access_manager->reveal());
    \Drupal::setContainer($container);

    // Default config
    $form = [];
    $return = $this->plugin->blockForm($form, $this->formState);

    $this->assertTrue($return['display']['use_site_logo']['#default_value']);
    $this->assertTrue($return['display']['use_site_name']['#default_value']);
    $this->assertTrue($return['display']['use_site_slogan']['#default_value']);
    $this->assertTrue($return['display']['use_site_credit_info']['#default_value']);

    $this->assertEquals('link', $return['display']['use_site_logo']['#description']['#type']);
    $this->assertEquals('link', $return['display']['use_site_name']['#description']['#type']);
    $this->assertEquals('link', $return['display']['use_site_slogan']['#description']['#type']);
    $this->assertEquals('link', $return['display']['use_site_credit_info']['#description']['#type']);

    // Altered config
    $this->plugin->setConfiguration([
      'use_site_logo' => FALSE,
      'use_site_name' => FALSE,
      'use_site_slogan' => FALSE,
      'use_site_credit_info' => FALSE
    ]);
    $return = $this->plugin->blockForm($form, $this->formState);

    $this->assertFalse($return['display']['use_site_logo']['#default_value']);
    $this->assertFalse($return['display']['use_site_name']['#default_value']);
    $this->assertFalse($return['display']['use_site_slogan']['#default_value']);
    $this->assertFalse($return['display']['use_site_credit_info']['#default_value']);
  }

  /**
   * Tests Block Form Access Denied to change theme and config settings
   *
   * @covers ::blockForm()
   */
  public function testBlockFormSettingAccessDenied() {
    // Set up the Service Container
    $access_manager = $this->prophesize(AccessManagerInterface::CLASS);
    $access_manager->checkNamedRoute("system.theme_settings_theme", ["theme" => null], null)->willReturn(FALSE);
    $access_manager->checkNamedRoute("system.site_information_settings", [], null)->willReturn(FALSE);
    $container = new ContainerBuilder();
    $container->set('access_manager', $access_manager->reveal());
    \Drupal::setContainer($container);

    // Access Denied to change theme and config settings
    $form = [];
    $return = $this->plugin->blockForm($form, $this->formState);
    $this->assertEquals(
      'Defined on the Theme Settings page. You do not have the appropriate permissions to change the site logo.',
      $return['display']['use_site_logo']['#description']
    );
    $this->assertEquals(
      'Defined on the Site Information page. You do not have the appropriate permissions to change the site name.',
      $return['display']['use_site_name']['#description']
    );
    $this->assertEquals(
      'Defined on the Site Information page. You do not have the appropriate permissions to change the site slogan.',
      $return['display']['use_site_slogan']['#description']
    );
    $this->assertEquals(
      'Defined on the Site Information page. You do not have the appropriate permissions to change the site credits information.',
      $return['display']['use_site_credit_info']['#description']
    );
  }

}
