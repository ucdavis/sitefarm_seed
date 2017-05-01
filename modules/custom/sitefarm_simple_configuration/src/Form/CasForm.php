<?php

namespace Drupal\sitefarm_simple_configuration\Form;

use Drupal\cas\Form\CasSettings;
use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class CasForm.
 *
 * @package Drupal\sitefarm_simple_configuration\Form
 */
class CasForm extends CasSettings {

  /**
   * The module handler to invoke the alter hook.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a \Drupal\sitefarm_simple_configuration\Form object.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param FactoryInterface $plugin_factory
   *   The condition plugin factory.
   * @param ModuleHandler $module_handler
   *   The module handler service.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
                              FactoryInterface $plugin_factory,
                              ModuleHandler $module_handler) {
    parent::__construct($config_factory, $plugin_factory);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.condition'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sitefarm_auth_simplecas_form';
  }

  /**
   * Hides form elements, while maintaining their values.
   *
   * @param $form
   * @param $group_name
   * @param $element_name
   */
  protected function hideElement(&$form, $group_name, $element_name) {
    $form[$group_name][$element_name]['#access'] = FALSE;
    $form[$group_name][$element_name]['#type'] = 'value';
    $form[$group_name][$element_name]['#value'] = $form[$group_name][$element_name]['#default_value'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $remove = ['server', 'general', 'gateway', 'proxy', 'debugging',
      'user_accounts' => ['restrict_password_management'],
      'logout' => ['enable_single_logout'],
    ];

    // Remove access to all unnecessary form elements, but pass the values to the form handlers
    foreach ($remove as $group_name => $group) {
      if (is_array($group)) {
        foreach ($group as $element_name) {
          $this->hideElement($form, $group_name, $element_name);
        }
      }
      else {
        $form[$group]['#access'] = FALSE;
        foreach ($form[$group] as $key => $element) {
          if (is_array($element) && isset($element['#default_value'])) {
            $this->hideElement($form, $group, $key);
          }
        }
      }
    }

    // Hide administrator and site builder roles.
    if ($this->moduleHandler->moduleExists('roleassign')) {
      $unassignable_roles = array_flip(roleassign_get_unassignable_roles());
      $form['user_accounts']['auto_assigned_roles']['#options'] = array_diff_key($form['user_accounts']['auto_assigned_roles']['#options'], $unassignable_roles);
    }

    // Better form usability.
    // "Forced Login" box should not be collapsed.
    $form['forced_login']['#open'] = TRUE;
    // "Forced Login" description needs to be more clear.
    $form['forced_login']['#description'] = t('Anonymous users will be forced to login through CAS when enabled.');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $new_enabled = $form_state->getValue(['forced_login', 'enabled']);
    $old_enabled = $form['forced_login']['enabled']['#default_value'];
    $new_paths = $form_state->getValue(['forced_login', 'paths']);
    $old_paths = [
      'pages' => $form['forced_login']['paths']['pages']['#default_value'],
      'negate' => $form['forced_login']['paths']['negate']['#default_value'],
    ];
    if ($new_enabled != $old_enabled || $new_paths != $old_paths) {
        // CAS forced_login changed. Clear page caches.
        Cache::invalidateTags(['rendered']);
    }

    parent::submitForm($form, $form_state);
  }

}
