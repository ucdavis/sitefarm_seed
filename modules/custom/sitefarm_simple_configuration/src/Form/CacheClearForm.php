<?php

namespace Drupal\sitefarm_simple_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CacheClearForm.
 *
 * @package Drupal\sitefarm_simple_configuration\Form
 */
class CacheClearForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cache_clear_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['clear_all_caches'] = array(
      '#type' => 'markup',
      '#markup' => $this->t('Clicking this button will clear the entire site cache.'),
    );

    $form['actions']['submit']['#value'] = $this->t('Clear all caches');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->clearCaches();
  }

  /**
   * Clear all Drupal caches and set a message
   *
   * @codeCoverageIgnore
   */
  protected function clearCaches() {
    drupal_flush_all_caches();
    drupal_set_message($this->t('Caches cleared.'));
  }

}
