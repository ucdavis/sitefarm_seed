<?php
/**
 * @file
 * Enables modules and site configuration for a SiteFarm site installation.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function sitefarm_seed_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Fetch Helper services
  $helper = \Drupal::service('sitefarm_seed.profile_install');

  // Hide some messages from various modules that are just too chatty.
  $helper->defaultContentModuleCleanup();
  $helper->clearMessages();

  // Use the site email address for the contact form
  $helper->useMailInContactForm($form);

  // Set defaults and hide the region settings
  $helper->hideAndSetDefaultRegion($form);

  // Remove update options
  $helper->removeUpdateNotificationOptions($form);
}
