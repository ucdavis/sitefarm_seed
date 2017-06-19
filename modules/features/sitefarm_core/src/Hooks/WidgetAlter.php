<?php

namespace Drupal\sitefarm_core\Hooks;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class WidgetAlter.
 *
 * Helper utility to break out widget procedural code in hooks
 *
 * @package Drupal\sitefarm_core\Hooks
 */
class WidgetAlter {
  // Ensure we have the ability to use string translation $this->t().
  use StringTranslationTrait;

  /**
   * Set the help text of the Body Summary field
   *
   * @param $element
   * @param $text
   */
  public function setBodyFieldHelpText(&$element, $text) {
    if (isset($element['#title']) && $element['#title'] == 'Body') {
      $element['summary']['#description'] = $this->t($text);
    }
  }

  /**
   * Add a new process function to change the title attribute of the primary
   * image.
   *
   * @param $element
   */
  public function setPrimaryImageTitleText(&$element) {
    if (isset($element['#field_name']) && $element['#field_name'] == 'field_sf_primary_image') {
      $element['#process'][] = [$this, 'primaryImageTitleProcess'];
    }
  }

  /**
   * Element #process callback function. Change Title attribute to say "Caption"
   *
   * @see setPrimaryImageTitleText()
   *
   * @param $element
   * @param FormStateInterface $form_state
   * @param $form
   * @return mixed
   */
  public function primaryImageTitleProcess($element, FormStateInterface $form_state, $form) {
    $element['title']['#title'] = $this->t('Caption');
    $element['title']['#description'] = '';

    return $element;
  }

  /**
   * Set the help text for FocalPoint UI
   *
   * @param $element
   */
  public function setFocalPointHelpText(&$element) {
    // Exit early if there is no process key
    if (!isset($element['#process'])) {
      return;
    }

    foreach ($element['#process'] as $process) {
      if (is_array($process) && in_array('Drupal\focal_point\Plugin\Field\FieldWidget\FocalPointImageWidget', $process)) {
        $element['#process'][] = [$this, 'focalPointHelpProcess'];
      }
    }
  }

  /**
   * Callback function to the setFocalPointHelpText() method for setting the
   * help text of the focal point widget
   *
   * @see setFocalPointHelpText()
   *
   * @param $element
   * @param FormStateInterface $form_state
   * @param $form
   * @return mixed
   */
  public function focalPointHelpProcess($element, FormStateInterface $form_state, $form) {
    if (isset($element['alt']['#access']) && $element['alt']['#access']) {
      $element['focal_point_how'] = array(
        '#markup' => '<p><strong>' .
          $this->t('What\'s the plus sign for? ') . '</strong>' .
          $this->t('Wherever the crosshair is placed is guaranteed to be in any cropped image.')
          . '</p>',
        '#weight' => $element['alt']['#weight'] - 1,
      );
    }

    return $element;
  }
}
