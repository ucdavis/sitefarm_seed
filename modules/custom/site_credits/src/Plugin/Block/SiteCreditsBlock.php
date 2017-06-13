<?php

namespace Drupal\site_credits\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SiteCreditsBlock' block.
 *
 * @Block(
 *  id = "site_credits_block",
 *  admin_label = @Translation("Site Credits"),
 * )
 */
class SiteCreditsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates a SiteCreditsBlock instance.
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
      'use_site_logo' => TRUE,
      'use_site_name' => TRUE,
      'use_site_slogan' => FALSE,
      'use_site_credit_info' => TRUE,
      'label_display' => FALSE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $site_logo_description = $this->t('Defined on the Theme Settings page. You do not have the appropriate permissions to change the site logo.');
    $site_name_description = $this->t('Defined on the Site Information page. You do not have the appropriate permissions to change the site name.');
    $site_slogan_description = $this->t('Defined on the Site Information page. You do not have the appropriate permissions to change the site slogan.');
    $site_info_description = $this->t('Defined on the Site Information page. You do not have the appropriate permissions to change the site credits information.');

    // Get the theme.
    $theme = $form_state->get('block_theme');

    // Provide a link to the Theme Settings page
    // if the user has access to administer themes.
    $url_system_theme_settings_theme = new Url('system.theme_settings_theme', array('theme' => $theme));
    if ($url_system_theme_settings_theme->access()) {
      $site_logo_description = [
        '#type' => 'link',
        '#title' => $this->t('Defined on the Theme Settings page.'),
        '#url' => $url_system_theme_settings_theme,
      ];
    }

    // Provide link to Site Information page if the user has access to
    // administer site configuration.
    $url_system_site_information_settings = new Url('system.site_information_settings');
    if ($url_system_site_information_settings->access()) {
      $site_information_link = [
        '#type' => 'link',
        '#title' => $this->t('Defined on the Site Information page.'),
        '#url' => $url_system_site_information_settings,
      ];
      $site_name_description = $site_information_link;
      $site_slogan_description = $site_information_link;
      $site_info_description = $site_information_link;
    }

    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Display Settings'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );

    $form['display']['use_site_logo'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Site Logo'),
      '#description' => $site_logo_description,
      '#default_value' => $this->configuration['use_site_logo'],
    );
    $form['display']['use_site_name'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Site Name'),
      '#description' => $site_name_description,
      '#default_value' => $this->configuration['use_site_name'],
    );
    $form['display']['use_site_slogan'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Site Slogan'),
      '#description' => $site_slogan_description,
      '#default_value' => $this->configuration['use_site_slogan'],
    );
    $form['display']['use_site_credit_info'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display the Site Credits Information'),
      '#description' => $site_info_description,
      '#default_value' => $this->configuration['use_site_credit_info'],
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
    $site_config = $this->configFactory->get('system.site');

    $build['site_logo'] = array(
      '#theme' => 'image',
      '#uri' => $this->getLogoPath(),
      '#alt' => $this->t('Home'),
      '#access' => $config['use_site_logo'],
    );

    $build['site_name'] = array(
      '#markup' => $site_config->get('name'),
      '#access' => $config['use_site_name'],
    );

    $build['site_slogan'] = array(
      '#markup' => $site_config->get('slogan'),
      '#access' => $config['use_site_slogan'],
    );

    $build['site_credit_info'] = array(
      '#access' => $config['use_site_credit_info'],
      '#type' => 'processed_text',
      '#text' => $site_config->get('credit_info')['value'],
      '#format' => $site_config->get('credit_info')['format'],
    );

    return $build;
  }

  /**
   * Method wrapper of theme_get_settings so that it can be mocked for Unit Test
   *
   * @return string
   *
   * @codeCoverageIgnore
   */
  protected function getLogoPath() {
    return theme_get_setting('logo.url');
  }
}
