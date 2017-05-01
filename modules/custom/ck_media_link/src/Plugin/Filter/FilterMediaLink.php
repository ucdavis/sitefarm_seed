<?php

namespace Drupal\ck_media_link\Plugin\Filter;

use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Path\PathValidator;
use Drupal\Core\Path\AliasManager;

/**
 * @Filter(
 *   id = "filter_media_link",
 *   title = @Translation("Media Link Filter"),
 *   description = @Translation("Wrap Media Link widgets with a link."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterMediaLink extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Path\PathValidator $pathValidator
   */
  protected $pathValidator;

  /**
   * @var \Drupal\Core\Path\AliasManager $aliasManager
   */
  protected $aliasManager;

  /**
   * Creates a UCCreditsBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Path\PathValidator $pathValidator
   *   Path Validator service.
   * @param \Drupal\Core\Path\AliasManager $aliasManager
   *   Alias Manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PathValidator $pathValidator, AliasManager $aliasManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pathValidator = $pathValidator;
    $this->aliasManager = $aliasManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path.validator'),
      $container->get('path.alias_manager')
    );
  }

  public function process($text, $langcode) {
    // Pattern to find media-link widgets
    $pattern = '/<div\s+class="media-link__wrapper([^"])*"\s?data-url="(([^"])+)"(.|\s)+?(<\/div>\s*){2}(<\/div>)/';
    // Add the responsive wrapper class and data-attribute
    $text = preg_replace_callback($pattern, [$this, 'linkReplace'], $text);

    return new FilterProcessResult($text);
  }

  /**
   * Add the responsive wrapper class and data-attribute
   *
   * @param $matches
   * @return mixed
   */
  protected function linkReplace($matches) {
    // $matches[0]: The complete string
    // $matches[2]: URL

    $text = $matches[0];
    $path = $matches[2];

    $return = '<a href="' . $this->formatPath($path) . '" class="media-link">' . $text . '</a>';

    return $return;
  }

  /**
   * Check that a path is valid and format it correctly
   *
   * @param string $path
   *   The url whether internal or external to check against
   */
  public function formatPath($path) {
    $path = trim($path);

    // Add a preceding slash to internal url
    if (!preg_match('/^http/', $path) && !preg_match('/^\//', $path)) {
      $path = '/' . $path;
    }

    // If this is not valid internal path such as node/1 or external url
    if (!$this->pathValidator->isValid($path)) {
      // If this is not a valid internal path alias
      $alias = $this->aliasManager->getAliasByPath($path);
      if ($path == $alias) {
        // Only add one slash since we already added one earlier
        return 'http:/' . $path;
      }
    }

    return $path;
  }
}
