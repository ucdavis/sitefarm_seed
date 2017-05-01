<?php

namespace Drupal\block_style_plugins\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for Block style plugins.
 */
interface BlockStyleInterface extends PluginInspectionInterface {

  /**
   * Returns the configuration form elements specific to a block configuration.
   *
   * This code will be run as part of a form alter so that the current blocks
   * configuration will be available to this method.
   *
   * @param array $form
   *   The form definition array for the block configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array $form
   *   The renderable form array representing the entire configuration form.
   */
  public function prepareForm($form, FormStateInterface $form_state);

  /**
   * Returns an array of field elements that should be injected into the block
   * configuration form.
   *
   * @param array $form
   *   The form definition array for the block configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array $elements
   *   A list of all form field elements that will allow setting styles.
   */
  public function formElements($form, FormStateInterface $form_state);

  /**
   * Returns a customized form array with new form settings for styles.
   *
   * @param array $form
   *   The form definition array for the block configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array $form
   *   The renderable form array representing the entire configuration form.
   */
  public function formAlter($form, FormStateInterface $form_state);

  /**
   * Adds block style specific submission handling for the block form.
   *
   * @param array $form
   *   The form definition array for the full block configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm($form, FormStateInterface $form_state);

  /**
   * Builds and returns the renderable array for this block style plugin.
   *
   * @param array $variables
   *
   * @return array
   *   A renderable array representing the content of the block.
   */
  public function build(array $variables);

  /**
   * Determine if configuration should be excluded from certain blocks when a
   * block plugin id or block content type is passed from a plugin.
   *
   * @return boolean
   */
  public function exclude();

  /**
   * Determine if configuration should be only included on certain blocks when a
   * block plugin id or block content type is passed from a plugin.
   *
   * @return boolean
   */
  public function includeOnly();

  /**
   * Create a list of style configuration defaults
   *
   * @return array
   */
  public function defaultStyles();

  /**
   * Sets the style configuration for this plugin instance.
   *
   * @param array $styles
   */
  public function setStyles(array $styles);

  /**
   * Retrieve a list of style configuration
   *
   * @return array
   */
  public function getStyles();
}
