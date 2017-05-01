<?php

namespace Drupal\ck_autosave\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the "autosave" plugin.
 *
 * NOTE: The plugin ID ('id' key) corresponds to the CKEditor plugin name.
 * It is the first argument of the CKEDITOR.plugins.add() function in the
 * plugin.js file.
 *
 * @CKEditorPlugin(
 *   id = "autosave",
 *   label = @Translation("Autosave")
 * )
 */
class CKEditorAutosave extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface, CKEditorPluginContextualInterface {


  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [];
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function getFile() {
    // Make sure that the path to the plugin.js matches the file structure of
    // the CKEditor plugin you are implementing.
    return base_path() . 'libraries/autosave/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    $plugins = $editor->getSettings()['plugins'];

    if (isset($plugins['autosave'])) {
      return $editor->getSettings()['plugins']['autosave']['enable'];
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    $form['enable'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable AutoSave'),
      '#description' => $this->t('Automatically saves the content (via HTML5 LocalStorage) temporarly (for example when a login session times out). And after the content is saved it can be restored when the editor is reloaded.'),
      '#default_value' => !empty($settings['plugins']['autosave']['enable']) ? $settings['plugins']['autosave']['enable'] : FALSE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
//      'autosave_SaveKey' => 'autosaveKey',
      'autosave_saveDetectionSelectors' => "a[href^='javascript:__doPostBack'][id*='Save'],a[id*='Cancel'],.form-submit",
//      'autosave_NotOlderThen' => 1440,
//      'autosave_saveOnDestroy' => FALSE,
      'autosave_messageType' => 'no',
//      'autosave_delay' => 10,
//      'autosave_diffType' => 'sideBySide'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return ['notification'];
  }

}
