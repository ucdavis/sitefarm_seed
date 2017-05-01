<?php

/**
 * @file
 * Contains \SiteFarmOneThemeSubContext.
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;

/**
 * Sub context for SiteFarm step definitions.
 */
class SiteFarmOneThemeSubContext extends DrupalSubContextBase implements DrupalSubContextInterface {

  /**
   * The Mink context.
   *
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * Defaults for the theme configuration
   *
   * @var array
   */
  protected $themeConfigDefaults = array();

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts(BeforeScenarioScope $scope) {
    $environment = $scope->getEnvironment();

    $this->minkContext = $environment->getContext('Drupal\DrupalExtension\Context\MinkContext');
  }

  /**
   * Reset any default theme configuration
   *
   * @AfterScenario @api
   */
  public function restoreDefaultThemeConfiguration($event) {
    if (empty($this->themeConfigDefaults)) {
      return;
    }

    // Get the Sitefarm One theme config settings
    $config = \Drupal::service('config.factory')->getEditable('sitefarm_one.settings');

    // Set the default theme values.
    foreach ($this->themeConfigDefaults as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();
  }

  /**
   * @Given the Sitefarm One theme setting :key is set to :value
   */
  public function theSitefarmOneThemeSettingIsSetTo($key, $value)
  {
    // Get the Sitefarm One theme config settings
    $config = \Drupal::service('config.factory')->getEditable('sitefarm_one.settings');

    // Store default configuration if not already set
    if (!isset($this->themeConfigDefaults[$key])) {
      $this->themeConfigDefaults[$key] = $config->get($key);
    }


    // Set and save new theme value.
    $config->set($key, $value)->save();
  }


}
