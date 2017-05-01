<?php

namespace Drupal\rss_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\url_embed\UrlEmbedHelperTrait;
use Drupal\Core\Render\Markup;

/**
 * Provides a 'RssFeedBlock' block.
 *
 * @Block(
 *  id = "rss_feed_block",
 *  admin_label = @Translation("RSS Feed"),
 * )
 */
class RssFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'rss_url' => FALSE,
      'items_count' => 5,
      'feed_text' => FALSE,
      'text_cutoff' => FALSE,
      'more_button' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Display Settings'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );

    $form['display']['rss_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('RSS URL'),
      '#description' => $this->t('Add a RSS Feed Url'),
      '#default_value' => $this->configuration['rss_url'],
      '#required' => TRUE,
    );

    $form['display']['items_count'] = array(
      '#type' => 'number',
      '#title' => $this->t('Number of Posts:'),
      '#max' => 50,
      '#default_value' => $this->configuration['items_count'],
      '#description' => $this->t('How many posts would you like to display?'),
    );

    $form['display']['feed_text'] = array(
      '#type' => 'select',
      '#title' => $this->t('Display feed text'),
      '#description' => $this->t('Choose how much of the feed appears.'),
      '#options' => array(
        '' => $this->t('None'),
        'snippet' => $this->t('Snippet'),
        'full' => $this->t('Full'),
        'paragraph' => $this->t('Paragraph'),
      ),
      '#default_value' => $this->configuration['feed_text'],
    );

    $form['display']['text_cutoff'] = array(
      '#type' => 'select',
      '#title' => $this->t('Paragraph character cutoff length.'),
      '#options' => array(
        '250' => $this->t('250'),
        '500' => $this->t('500'),
        '1000' => $this->t('1000'),
      ),
      '#default_value' => $this->configuration['text_cutoff'],
      '#states' => array(
        'visible' => array(
          ':input[name="settings[display][feed_text]"]' => array('value' => 'paragraph'),
        ),
      ),
    );

    // More Button
    $form['display']['more_button'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display a "More" button'),
      '#default_value' => $this->configuration['more_button'],
      '#description' => $this->t('Check this box if a button to load in more feed items should appear at the bottom of the feed.'),
    );


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    foreach ($values['display'] as $key => $value) {
      $this->setConfigurationValue($key, $form_state->getValue(array('display', $key)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the config for this block
    $config = $this->getConfiguration();

    $build = [
      'items_count' => $config['items_count'],
      'feed_text' => $config['feed_text'],
      'text_cutoff' => $config['text_cutoff'],
      'more_button' => $config['more_button'],
      '#attached' => array(
        'library' =>  array(
          'rss_feed_block/rss_feed_block'
        ),
      ),
    ];

    return $build;
  }
}
