<?php

namespace Drupal\block_style_plugins\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Block style item annotation object.
 *
 * @see \Drupal\block_style_plugins\Plugin\BlockStyleManager
 * @see plugin_api
 *
 * @Annotation
 */
class BlockStyle extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * Block types to exclude.
   *
   * @var array
   */
  public $exclude = array();

  /**
   * Include only these block types.
   *
   * @var array
   */
  public $include = array();

}
