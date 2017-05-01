<?php

namespace Drupal\block_style_plugins\Plugin\BlockStyle;

use Drupal\block_style_plugins\Plugin\BlockStyleBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'SampleBlockStyle' block.
 *
 * @BlockStyle(
 *  id = "sample_block_style",
 *  label = @Translation("Sample Block Style"),
 *  include = {
 *    "block_plugin_base_id"
 *  }
 * )
 */
class SampleBlockStyle extends BlockStyleBase {

  /**
   * {@inheritdoc}
   */
  public function defaultStyles() {
    return ['sample_class' => ''];
  }

  /**
   * {@inheritdoc}
   */
  public function formElements($form, FormStateInterface $form_state) {
    // Title Style
    $elements['sample_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Add a custom class to this block'),
      '#description' => $this->t('Do not add the "period" to the start of the class'),
      '#default_value' => $this->styles['sample_class'],
    );

    return $elements;
  }

}
