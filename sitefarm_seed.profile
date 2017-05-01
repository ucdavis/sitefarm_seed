<?php
/**
 * @file
 * Enables modules and site configuration for a SiteFarm site installation.
 */

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function sitefarm_seed_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Hide some messages from various modules that are just too chatty.
  sitefarm_seed_module_cleanup();
  sitefarm_seed_clear_messages();

  $form['#submit'][] = 'sitefarm_seed_form_install_configure_submit';

  // Set defaults and hide the region settings
  $form['regional_settings']['#type'] = 'hidden';
  $form['regional_settings']['site_default_country']['#default_value'] = 'US';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'America/Los_Angeles';

  // Remove update options
  unset($form['update_notifications']);
}

/**
 * Submission handler to sync the contact.form.feedback recipient.
 */
function sitefarm_seed_form_install_configure_submit($form, FormStateInterface $form_state) {
  $site_mail = $form_state->getValue('site_mail');
  ContactForm::load('feedback')->setRecipients([$site_mail])->trustData()->save();
}

/**
 * Clear all 'notification' type messages that may have been set.
 */
function sitefarm_seed_clear_messages() {
  drupal_get_messages('status', TRUE);
  drupal_get_messages('warning', TRUE);
  drupal_get_messages('completed', TRUE);
}

/**
 * Uninstall module used for site install but not needed for day to day.
 */
function sitefarm_seed_module_cleanup() {
  \Drupal::service('module_installer')->uninstall([
    'default_content',
    'rest',
    'hal',
    'serialization',
  ]);
}
