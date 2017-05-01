<?php

namespace Drupal\external_media_embed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\url_embed\UrlEmbedHelperTrait;
use Drupal\Core\Render\Markup;

/**
 * Provides a 'ExternalMediaEmbedBlock' block.
 *
 * @Block(
 *  id = "external_media_embed_block",
 *  admin_label = @Translation("External Media"),
 * )
 */
class ExternalMediaEmbedBlock extends BlockBase {
  use UrlEmbedHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'embed_url' => FALSE,
      'label_display' => FALSE,
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

    $form['display']['embed_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Embed URL'),
      '#description' => $this->t('Add a video or other external embeddable from third-party services such as a Youtube or Vimeo. It should be a URL such as https://youtu.be/PAwB_t_iM7U or https://vimeo.com/79504673'),
      '#default_value' => $this->configuration['embed_url'],
      '#required' => TRUE,
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
    $build = array();

    // Get the config for this block
    $config = $this->getConfiguration();

    try {
      if ($info = $this->urlEmbed()->getEmbed($config['embed_url'])) {
        $url_output = $info->getCode();
        $ratio = $info->aspectRatio;

        // Wrap the embed code in a container to make it responsive
        if ($ratio) {
          $build = [
            '#theme' => 'responsive_embed',
            '#ratio' => $ratio,
            '#url_output' => $url_output,
            '#attached' => array(
              'library' =>  array(
                'url_embed/url_embed.responsive_styles'
              ),
            ),
          ];
        }
        else {
          $build = [
            '#markup' => Markup::create($url_output),
          ];
        }
      }
    }

    catch (\Exception $exception) {
      watchdog_exception('url_embed', $exception);
    }

    return $build;
  }
}
