<?php

namespace Drupal\sitefarm_core\Hooks;
use Drupal\Core\Extension\ModuleHandler;

/**
 * Class Preprocess.
 *
 * Helper utility for preprocess functions to break out procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class Preprocess {

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * Preprocess constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   */
  public function __construct(ModuleHandler $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
  }

  public function fixMetatagFrontTitle(array &$variables) {
    // The metatag module ads a pipe to the meta title before it gets to the theme
    // In some cases it will have a prefixed pipe on the home page. Remove it.
    if ($this->moduleHandler->moduleExists('metatag')
      && isset($variables['head_title']['title'])
      && substr($variables['head_title']['title'], 0, 2) === "| ") {
      $variables['head_title']['title'] = substr($variables['head_title']['title'], 2);
    }
  }

}
