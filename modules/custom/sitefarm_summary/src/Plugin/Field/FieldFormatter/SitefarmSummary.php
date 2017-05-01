<?php

namespace Drupal\sitefarm_summary\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Plugin implementation of the 'sitefarm_summary' formatter.
 *
 * @FieldFormatter(
 *   id = "sitefarm_summary",
 *   label = @Translation("Summary only"),
 *   field_types = {
 *     "text_with_summary"
 *   }
 * )
 */
class SitefarmSummary extends FormatterBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $summary[] = $this->t('Displays the summary only for text fields that have a summary.');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $element[$delta] = array(
        '#type' => 'markup',
        '#markup' => $item->summary,
      );
    }

    return $element;
  }
}