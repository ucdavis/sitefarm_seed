<?php

namespace Drupal\sitefarm_simple_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomBlockTypesForm.
 *
 * @package Drupal\sitefarm_simple_configuration\Form
 */
class CustomBlockTypesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'customize_block_types';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $default = $this->config('sitefarm_core.settings')->get('generate_custom_block_title');


    $form['custom_block_types'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Auto generate <b>Custom Block</b> instance titles'),
      '#default_value' => $default,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory()->getEditable('sitefarm_core.settings');

    $data = $form_state->getValue('custom_block_types');

    $config->set('generate_custom_block_title', $data);
    $config->save();
    drupal_set_message($this->t('Configuration has been saved.'));
  }

}
