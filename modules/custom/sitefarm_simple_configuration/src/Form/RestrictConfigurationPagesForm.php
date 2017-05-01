<?php

namespace Drupal\sitefarm_simple_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RestrictConfigurationPagesForm.
 *
 * @package Drupal\sitefarm_simple_configuration\Form
 */
class RestrictConfigurationPagesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['sitefarm_simple_configuration.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'restrict_configuration_pages';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $routes = $this->config('sitefarm_simple_configuration.settings')->get('restricted_routes');

    $form['restricted_routes'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Route names to restrict'),
      '#description' => $this->t('Add one route per line to restrict that page from access to all but the Administrator and Site Builder. These should be actual routes and not urls. You can find route names for a path using Drupal Console with "$ drupal route:debug".'),
      '#default_value' => implode("\n", $routes),
      '#rows' => 20,
    );

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
    $config = $this->config('sitefarm_simple_configuration.settings');

    $data = array_map('trim', explode("\n", $form_state->getValue('restricted_routes')));

    $config->set('restricted_routes', $data);
    $config->save();

    // Rebuild the routes since we have added new ones
    \Drupal::service("router.builder")->rebuild();
  }

}
