<?php

namespace Drupal\sitefarm_core;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityRepositoryInterface;

/**
 * Class BlockConfigFormHelpers.
 *
 * Universal helper utilities for using and manipulating Block config forms.
 *
 * @package Drupal\sitefarm_core
 */
class BlockConfigFormHelpers {
  // Ensure we have the ability to use string translation $this->t().
  use StringTranslationTrait;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * BlockConfigFormHelpers constructor.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   */
  public function __construct(EntityRepositoryInterface $entityRepository) {
    $this->entityRepository = $entityRepository;
  }

  /**
   * Get the Block content bundle that a configuration form supports
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return string
   *   Block Content entity Bundle Name
   */
  public function getBlockContentBundle(FormStateInterface $form_state) {
    if ($entity = $this->getBlockContentEntity($form_state)) {
      return $entity->bundle();
    }

    return FALSE;
  }

  /**
   * Get the Block Config Entity object instance
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\block\Entity\Block
   */
  public function getBlockConfigEntity(FormStateInterface $form_state) {
    // Get the block entity
    return $form_state->getFormObject()->getEntity();
  }

  /**
   * Get the Block Entity plugin object instance
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Core\Block\BlockPluginInterface
   */
  public function getBlockEntityPlugin(FormStateInterface $form_state) {
    // Get the block plugin
    $entity = $this->getBlockConfigEntity($form_state);
    return $entity->getPlugin();
  }

  /**
   * Get the Block content bundle that a configuration form supports
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\block_content\Entity\BlockContent
   */
  public function getBlockContentEntity(FormStateInterface $form_state) {
    // Get the block plugin
    $block_plugin = $this->getBlockEntityPlugin($form_state);

    $base_id = $block_plugin->getBaseId();
    $uuid = $block_plugin->getDerivativeId();

    if ($base_id == 'block_content') {
      $block_content_entity = $this->entityRepository->loadEntityByUuid('block_content', $uuid);

      if ($block_content_entity) {
        return $block_content_entity;
      }
    }

    return FALSE;
  }

  /**
   * Hide the block title checkbox from the configuration page
   *
   * @param $form
   */
  public function hideBlockTitleCheckbox(&$form) {
    $form['settings']['label_display']['#prefix'] = $this->t('The Block Title will not be displayed.');
    $form['settings']['label_display']['#type'] = 'hidden';
    $form['settings']['label_display']['#default_value'] = FALSE;
  }

  /**
   * Hide the block title checkbox from the configuration page
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function unCheckBlockTitle(&$form, FormStateInterface $form_state) {
    $entity = $this->getBlockConfigEntity($form_state);
    if (!$entity->id()) {
      $form['settings']['label_display']['#default_value'] = FALSE;
    }
  }

}
