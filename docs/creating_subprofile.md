# Create a Sub-Profile from Sitefarm Seed

This is all made possible by [Patch 1356276](https://www.drupal.org/node/1356276#comment-12017233) which allow profiles to provide a base/parent profile and load them in the correct order.

## Download or Clone Sitefarm Distro Template

[Download or Clone](https://github.com/ucdavis/sitefarm-distro-template) from repo.

This is a SiteFarm Composer-based Drupal distribution template. Place this in your local development environment, and follow the [README instructions on GitHub](https://github.com/ucdavis/sitefarm-distro-template/blob/master/README.md) to install. The README also contains instructions on how to update and maintain the distro template.

## Create Your Own Sub-Profile

If you navigate to `/web/profiles` you will see two, the `sitefarm_seed` profile which only exists to extended by a sub-profile, and `sitefarm_subprofile` which only exists as an example of how to extend `sitefarm_seed`. 

Use the `sitefarm_subprofile` as an example of how to build your sub-profile. You may name your sub-profile anything you want using Drupal coding standards. 

For Example, your new sub-profile files and directories could look something like:

* my_custom_subprofile
    * my_custom_subprofile.info.yml
    * my_custom_subprofile.profile

### Configuration

This example profile contains custom configuration for Bartik, `/web/profiles/sitefarm_subprofile/config/install` and will want to be retained.

Add your custom configuration files to the `/config/install/` directory in your sub-profile.

### Info File

The heart of the sub-profile is the `{profile name}.info.yml`. The items you will want to update here are the name, description and distribution name.
 
~~~~
 name: New Sub-Profile
 type: profile
 description: 'A fast and feature-rich distribution for our university. A sub-profile of SiteFarm Seed'
 # version: VERSION
 core: 8.x
 
 # Force the installation of this profile since it is a distribution
 distribution:
   name: My Custom Distro
 
 # Base profile
 base profile: sitefarm_seed
 
 # Required modules
 # dependencies:
   # core
   # contrib
   # SiteFarm
 
 themes:
   - seven
   - bartik
~~~~

You may also want to uncomment and add dependencies for your distribution.

### Profile

Looking at the example file in the subprofile example `/sitefarm_subprofile/profile` you will see we are implementing `hook_form_FORM_ID_alter()` in order to alter the site configuration form. You will want to include this sample code in your custom sub-profile, and remember to replace the hook with your sub-profile name.

example: 

~~~~
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
function my_custom_subprofile_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Fetch Helper services
  /** @var \Drupal\sitefarm_seed\ProfileInstall $helper */
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
~~~~

Notice we have added `$helper = \Drupal::service('sitefarm_seed.profile_install');` which provides the `profile_install` class as a variable and allows access to it's methods. You can alter some of the helper methods in this implementation, like `hideAndSetDefaultRegion($form)`, by passing in arguments for the `$country` and `$timezone` found in the methods at `/sitefarm-distro-template/web/profiles/sitefarm_seed/src/ProfileInstall.php`.

You can also further alter the form if there is some other data or configuration you would like to capture.

Place any additional profile related php in this file.

### Sub-Profile Directory

Place any additional profile code in your sub-profiles main directory like you would any other profile or module.
