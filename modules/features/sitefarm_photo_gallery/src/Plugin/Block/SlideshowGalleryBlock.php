<?php

namespace Drupal\sitefarm_photo_gallery\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides a 'SlideshowGalleryBlock' block.
 *
 * @Block(
 *  id = "slideshow_gallery_block",
 *  admin_label = @Translation("Slideshow Photo Gallery"),
 * )
 */
class SlideshowGalleryBlock extends BlockBase implements ContainerFactoryPluginInterface {
  // todo: use a view for the display

  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates a SlideshowGalleryBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'show_title' => TRUE,
      'lazy_load' => TRUE,
      'slider_nav' => FALSE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Fetch the default node
    $node = FALSE;
    if (isset($this->configuration['gallery'])) {
      $node = Node::load($this->configuration['gallery']);
    }

    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Slideshow Settings'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );
    $form['display']['gallery'] = array(
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Gallery Title'),
      '#target_type' => 'node',
      '#selection_settings' => array(
        'target_bundles' => array('sf_photo_gallery'),
      ),
      '#description' => $this->t('Select the gallery to be displayed by typing the title of the gallery. As you type, suggestions will appear for you to select a gallery.'),
      '#default_value' => $node,
      '#required' => TRUE,
    );
    $form['display']['show_title'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show the Gallery Title on the first slide.'),
      '#default_value' => $this->configuration['show_title'],
    );
    $form['display']['lazy_load'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Lazy Load the images'),
      '#description' => $this->t('Progressively download images so that pages load faster.'),
      '#default_value' => $this->configuration['lazy_load'],
    );
    $form['display']['slider_nav'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Slider Navigation Thumbnails'),
      '#description' => $this->t('Display a list of thumbnail images for navigation the gallery.'),
      '#default_value' => $this->configuration['slider_nav'],
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
    // Renderer for cache busting
    $renderer = \Drupal::service('renderer');

    // Get the config for this block
    $config = $this->getConfiguration();

    // Fetch the photo gallery node
    $nid = $config['gallery'];
    $node = Node::load($nid);

    // Return empty if there is no node attached (something went wrong)
    if (!$node) {
      return [];
    }

    $title = $node->getTitle();

    // Slides
    $field = $node->field_sf_gallery_photo;
    $props = $field->getValue();
    $slides = $field->referencedEntities();

    $style_full = ImageStyle::load('sf_slideshow_full');
    $style_thumb = ImageStyle::load('sf_slideshow_thumbnail');

    $slides_main = [];
    $slides_nav = [];

    foreach ($slides as $key => $file) {
      $uri = $file->getFileUri();

      // Create the main slides
      $image_build = [
        '#theme' => 'image_style',
        '#width' => $props[$key]['width'],
        '#height' => $props[$key]['height'],
        '#style_name' => 'sf_slideshow_full',
        '#alt' => $props[$key]['alt'],
        '#uri' => $uri,
      ];

      // Add the file entity to the cache dependencies.
      // This will clear our cache when this entity updates.
      $renderer->addCacheableDependency($image_build, $file);

      $slides_main[] = [
        'image' => $image_build,
        'src' => $style_full->buildUrl($uri),
        'alt' => $props[$key]['alt'],
        'caption' => $props[$key]['title'],
      ];

      // Create the slider nav thumbnails
      if ($config['slider_nav']) {
        $thumb_build = $image_build;
        $thumb_build['#style_name'] = 'sf_slideshow_thumbnail';

        $renderer->addCacheableDependency($thumb_build, $file);

        $slides_nav[] = [
          'image' => $thumb_build,
          'src' => $style_thumb->buildUrl($uri),
          'alt' => $props[$key]['alt'],
        ];
      }
    }

    // Create the build array with asset libraries already attached
    $build = [
      '#attached' => array(
        'library' =>  array(
          'sitefarm_photo_gallery/sitefarm_photo_gallery',
          'sitefarm_photo_gallery/sitefarm_photo_gallery.slick',
          'sitefarm_photo_gallery/sitefarm_photo_gallery.slick_theme',
        ),
      )
    ];

    // Set up the Main Slide array
    $title_parts = $this->splitTitle($title);
    $build['slideshow_main'] = [
      '#theme' => 'slideshow_main',
      '#show_title' => $config['show_title'],
      '#title_start' => $title_parts['title_start'],
      '#title_end' => $title_parts['title_end'],
      '#title_full' => $title,
      '#lazy_load' => $config['lazy_load'],
      '#slides' => $slides_main,
    ];

    // Set up the Navigation Slide array
    if (!empty($slides_nav)) {
      $build['slideshow_nav'] = [
        '#theme' => 'slideshow_nav',
        '#lazy_load' => $config['lazy_load'],
        '#slides' => $slides_nav,
      ];
    }

    return $build;
  }

  /**
   * Split a Title into 2 parts so they can be styled differently
   *
   * @param $text
   * @return array - title start, title_end
   */
  protected function splitTitle($text) {
    // Fetch the First word
    $title_start = FALSE;
    $title_end = FALSE;

    // Fetch the First word if longer than 2 characters or else the first 2 words
    $pattern = '/^((\S{3,})|(\S{1,2}\s\S+))/';

    preg_match($pattern, $text, $matches);

    if (!empty($matches)) {
      $title_start = $matches[0];
      $title_end = preg_replace($pattern, '', $text);
    }

    return [
      'title_start' => $title_start,
      'title_end' => $title_end,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Rebuild the block when the node changes
    if (isset($this->configuration['gallery'])) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $this->configuration['gallery']));
    }
    else {
      // Return default tags instead.
      return parent::getCacheTags();
    }
  }
}
