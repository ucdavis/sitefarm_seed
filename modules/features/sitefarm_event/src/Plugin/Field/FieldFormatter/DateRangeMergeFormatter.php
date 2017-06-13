<?php

namespace Drupal\sitefarm_event\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldFormatter\DateTimeDefaultFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;


/**
 * Plugin implementation of the 'Merge' formatter for 'daterange' fields.
 *
 * This formatter renders the data range using <time> elements, with
 * configurable options for showing the day name and time.
 *
 * @FieldFormatter(
 *   id = "daterange_merge",
 *   label = @Translation("Merge"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class DateRangeMergeFormatter extends DateTimeDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'show_day_name' => TRUE,
      'show_time' => TRUE,
      'separator' => ' ~ ',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if (!empty($item->start_date) && !empty($item->end_date)) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
        $start_date = $item->start_date;
        /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
        $end_date = $item->end_date;

        $elements[$delta] = $this->buildDateRange($start_date, $end_date);
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // TODO: create long and short formats with option to choose

    $form['show_day_name'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Day Name'),
      '#description' => $this->t('Check the box if the Day name should be shown on single day dates.'),
      '#default_value' => $this->getSetting('show_day_name'),
    ];

    $form['show_time'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Time'),
      '#description' => $this->t('Check the box if the time of these dates should be shown.'),
      '#default_value' => $this->getSetting('show_time'),
    ];

    $form['separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Date & Time separator'),
      '#description' => $this->t('The string to separate the dates and times'),
      '#default_value' => $this->getSetting('separator'),
      '#states' => array(
        'visible' => array(
          ':input[name="fields[field_sf_dates][settings_edit_form][settings][show_time]"]' => array('checked' => TRUE),
        ),
      ),
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($this->getSetting('show_day_name')) {
      $summary[] = $this->t('Display Day Name: Yes');
    }

    if ($this->getSetting('show_time')) {
      $summary[] = $this->t('Display Time: Yes');

      if ($separator = $this->getSetting('separator')) {
        $summary[] = $this->t('Separator: %separator', ['%separator' => $separator]);
      }
    }

    return $summary;
  }

  /**
   * Creates a render array from a date object with ISO date attribute.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $start_date
   *   A date object.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $end_date
   *   A date object.
   *
   * @return array
   *   A render array.
   */
  protected function buildDateRange(DrupalDateTime $start_date, DrupalDateTime $end_date) {
    if ($this->getFieldSetting('datetime_type') == DateTimeItem::DATETIME_TYPE_DATE) {
      // A date without time will pick up the current time, use the default.
      datetime_date_default_time($start_date);
      datetime_date_default_time($end_date);
    }

    // Create the ISO date in Universal Time.
    $iso_date = $start_date->format("Y-m-d\TH:i:s") . 'Z';
    if ($start_date->format('U') !== $end_date->format('U')) {
      $iso_date .= '/' . $end_date->format("Y-m-d\TH:i:s") . 'Z';
    }

    $this->setTimeZone($start_date);
    $this->setTimeZone($end_date);

    $output = '';

    // Multi Day, Month, & Year - Dec 30, 2020 - Jan 2, 2021
    if ($start_date->format("y") !== $end_date->format("y")) {
      $output = $start_date->format("M j, Y") . ' - ' . $end_date->format("M j, Y");
    }
    // Multi Day & Month - Nov 24 - Dec 1, 2020
    elseif ($start_date->format("ym") !== $end_date->format("ym")) {
      $output = $start_date->format("M j") . ' - ' . $end_date->format("M j, Y");
    }
    // Multi Day - Nov 24-26, 2020
    elseif ($start_date->format("ymd") !== $end_date->format("ymd")) {
      $output = $start_date->format("M j") . '-' . $end_date->format("j, Y");
    }
    // Same Day - Tue, Nov 24, 2020
    else {
      if ($this->getSetting('show_day_name')) {
        $output = $start_date->format("D, ");
      }
      $output .= $start_date->format("M j, Y");
    }


    // If the Time should be shown
    if ($this->getSetting('show_time')) {
      $output .= $this->getSetting('separator');

      if ($start_date->format("g:ia") !== $end_date->format("g:ia")) {
        $output .= $start_date->format("g:ia") . ' - ' . $end_date->format("g:ia");
      }
      else {
        $output .= $start_date->format("g:ia");
      }
    }


    $build = [
      '#theme' => 'time',
      '#text' => $output,
      '#html' => FALSE,
      '#attributes' => [
        'datetime' => $iso_date,
      ],
      '#cache' => [
        'contexts' => [
          'timezone',
        ],
      ],
    ];

    return $build;
  }

}
