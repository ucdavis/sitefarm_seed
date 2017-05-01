<?php

namespace Drupal\block_style_plugins\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityRepository;
use Drupal\block\Entity\Block;

/**
 * Base class for Block style plugins.
 */
abstract class BlockStyleBase extends PluginBase implements BlockStyleInterface, ContainerFactoryPluginInterface  {

  /**
   * Plugin ID for the Block being configured.
   *
   * @var string
   */
  protected $pluginId;

  /**
   * Plugin instance for the Block being configured.
   *
   * @var object
   */
  protected $blockPlugin;

  /**
   * Bundle type for 'Block Content' blocks.
   *
   * @var string
   */
  protected $blockContentBundle;

  /**
   * Instance of the Entity Repository service.
   *
   * @var string
   */
  protected $entityRepository;

  /**
   * Style settings for the block styles.
   *
   * @var array
   */
  protected $styles;

  /**
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityRepository $entityRepository
   *   The Entity Repository object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityRepository $entityRepository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Store our dependency.
    $this->entityRepository = $entityRepository;
    // Store the plugin ID
    $this->pluginId = $plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function prepareForm($form, FormStateInterface $form_state) {
    // Get the current block config entity
    $entity = $form_state->getFormObject()->getEntity();

    // Set properties and configuration
    $this->blockPlugin = $entity->getPlugin();
    $this->setBlockContentBundle();

    // Check to see if this should only apply to includes or if it has been
    // excluded
    if ($this->includeOnly() && !$this->exclude()) {

      // Create a fieldset to contain style fields
      if (!isset($form['block_styles'])) {
        $form['block_styles'] = array(
          '#type' => 'fieldset',
          '#title' => $this->t('Block Styles'),
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          '#weight' => 0,
        );
      }

      $styles = $entity->getThirdPartySetting('block_style_plugins', $this->pluginId);
      $styles = is_array($styles) ? $styles : [];
      $this->setStyles($styles);

      // Create containers to place each plugin style settings into the styles
      // fieldset
      $form['third_party_settings']['block_style_plugins'][$this->pluginId] = [
        '#type' => 'container',
        '#group' => 'block_styles',
      ];

      // Allow plugins to add field elements to this form
      $elements = $this->formElements($form, $form_state);
      if ($elements) {
        $form['third_party_settings']['block_style_plugins'][$this->pluginId] += $elements;
      }

      // Allow plugins to alter this form
      $form = $this->formAlter($form, $form_state);

      // Add the submitForm method to the form
      array_unshift($form['actions']['submit']['#submit'], [$this, 'submitForm']);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function formElements($form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function formAlter($form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm($form, FormStateInterface $form_state) {
    return;
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $variables) {
    // Ensure that we have a block id
    if (empty($variables['elements']['#id'])) {
      return $variables;
    }

    // Load the block config entity
    $block = Block::load($variables['elements']['#id']);
    $styles = $block->getThirdPartySetting('block_style_plugins', $this->pluginId);

    if ($styles) {
      // Add all styles config to the $variables array
      $variables['block_styles'][$this->pluginId] = $styles;

      // Add each style value as a class
      foreach ($styles as $class) {
        // Don't put a boolean from a checkbox as a class
        if (is_int($class)) {
          continue;
        }

        $variables['attributes']['class'][] = $class;
      }
    }

    return $variables;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultStyles() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getStyles() {
    return $this->styles;
  }

  /**
   * {@inheritdoc}
   */
  public function setStyles(array $styles) {
    $this->styles = NestedArray::mergeDeep(
      $this->defaultStyles(),
      $styles
    );
  }

  /**
   * {@inheritdoc}
   */
  public function exclude() {
    $list = array();

    if (isset($this->pluginDefinition['exclude'])) {
      $list = $this->pluginDefinition['exclude'];
    }

    $block_plugin_id = $this->blockPlugin->getPluginId();

    if (!empty($list) && (in_array($block_plugin_id, $list) || in_array($this->blockContentBundle, $list))) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function includeOnly() {
    $list = array();

    if (isset($this->pluginDefinition['include'])) {
      $list = $this->pluginDefinition['include'];
    }

    $block_plugin_id = $this->blockPlugin->getPluginId();

    if (empty($list) || (in_array($block_plugin_id, $list) || in_array($this->blockContentBundle, $list))) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Set the block content bundle type
   */
  public function setBlockContentBundle() {
    $base_id = $this->blockPlugin->getBaseId();
    $uuid = $this->blockPlugin->getDerivativeId();

    if ($base_id == 'block_content') {
      $plugin = $this->entityRepository->loadEntityByUuid('block_content', $uuid);

      if ($plugin) {
        $this->blockContentBundle = $plugin->bundle();
      }
    }
  }

}
