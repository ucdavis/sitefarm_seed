<?php

namespace Drupal\Tests\ck_autosave\Unit\Plugin\CKEditorPlugin;

use Drupal\Tests\UnitTestCase;
use Drupal\ck_autosave\Plugin\CKEditorPlugin\CKEditorAutosave;
use Drupal\editor\Entity\Editor;
use Drupal\Core\Form\FormStateInterface;

/**
 * @coversDefaultClass \Drupal\ck_autosave\Plugin\CKEditorPlugin\CKEditorAutosave
 * @group ck_autosave
 */
class CKEditorAutosaveTest extends UnitTestCase {


  protected $editor;

  /**
   * @var \Drupal\sitefarm_custom_social_links\Plugin\SocialMediaLinks\Iconset\SiteFarmIconSet
   */
  protected $plugin;

  /**
   * Create the setup for plugin and __construct
   */
  protected function setUp()
  {
    parent::setUp();

    // stub email validator
    $this->editor = $this->prophesize(Editor::CLASS);
    $this->editor->getSettings()->willReturn(
      [
        'plugins' => [
          'autosave' => [
            'enable' => TRUE
          ]
        ]
      ]
    );

    $configuration = [];
    $plugin_id = 'ck_autosave';
    $plugin_definition['provider'] = 'ck_autosave';

    $this->plugin = new CKEditorAutosave(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * tests getButtons()
   *
   * @see ::getButtons()
   */
  public function testGetButtons() {
    $this->assertEmpty($this->plugin->getButtons());
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    // Make sure that the path to the plugin.js matches the file structure of
    // the CKEditor plugin you are implementing.
    return base_path() . 'libraries/autosave/plugin.js';
  }

  /**
   * Tests isEnabled
   *
   * @see ::isEnabled()
   */
  public function testIsEnabled() {
    // Autosave is enabled
    $this->assertTrue($this->plugin->isEnabled($this->editor->reveal()));

    // Autosave is disabled
    $this->editor->getSettings()->willReturn(
      ['plugins' => '']
    );
    $this->assertFalse($this->plugin->isEnabled($this->editor->reveal()));
  }

  /**
   * Tests settingsForm method
   *
   * @see ::settingsForm()
   */
  public function testSettingsForm() {
    $form = [];
    $form_state = $this->getMock(FormStateInterface::CLASS);

    // Autosave is enabled
    $expected = [
      'enable' => [
        '#type' => 'checkbox',
        '#title' => 'Enable AutoSave',
        '#description' => 'Automatically saves the content (via HTML5 LocalStorage) temporarly (for example when a login session times out). And after the content is saved it can be restored when the editor is reloaded.',
        '#default_value' => TRUE,
      ]
    ];

    $this->assertArrayEquals(
      $expected,
      $this->plugin->settingsForm($form, $form_state, $this->editor->reveal())
    );

    // Autosave is disabled
    $expected['enable']['#default_value'] = FALSE;
    $settings['plugins']['autosave']['enable'] = FALSE;
    $this->editor->getSettings()->willReturn($settings);

    $this->assertArrayEquals(
      $expected,
      $this->plugin->settingsForm($form, $form_state, $this->editor->reveal())
    );
  }

  /**
   * Tests getConfig
   *
   * @see ::getConfig()
   */
  public function testGetConfig() {
    $expected = [
      'autosave_saveDetectionSelectors' => "a[href^='javascript:__doPostBack'][id*='Save'],a[id*='Cancel'],.form-submit",
      'autosave_messageType' => 'no',
    ];

    $this->assertArrayEquals(
      $expected,
      $this->plugin->getConfig($this->editor->reveal())
    );
  }

  /**
   * Tests getDependencies
   *
   * @see ::getDependencies()
   */
  public function testGetDependencies() {
    $expected = ['notification'];

    $this->assertArrayEquals(
      $expected,
      $this->plugin->getDependencies($this->editor->reveal())
    );
  }

}
