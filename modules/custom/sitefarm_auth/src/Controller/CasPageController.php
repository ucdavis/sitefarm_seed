<?php

/**
 * @file
 * Contains \Drupal\sitefarm_auth\Controller\CasPageController class.
 */

namespace Drupal\sitefarm_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Utility\LinkGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for sitefarm_auth module.
 */
class CasPageController extends ControllerBase {

  /**
   * Create protected variables to use my services
   */
  protected $userAccount;
  protected $makeLink;

  /**
   * Let the class know that I need access to AccountProxy and LinkGenerator
   * and assign them to variables I can protect and access
   *
   * CasPageController constructor.
   * @param AccountProxy $account
   * @param LinkGenerator $linkGen
   */
  public function __construct(AccountProxy $account, LinkGenerator $linkGen) {
    $this->userAccount = $account;
    $this->makeLink = $linkGen;
  }

  /**
   * Use the create factory method to get access to the service
   * container(where services live) on creation of the object based on my class.
   *
   * current_user and link_generator are the names of the service from the service
   * container(Services found in the service API)
   *
   * @param ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static ($container->get('current_user'), $container->get('link_generator'));
  }

  /**
   * Returns a page title.
   */
  public function getTitle() {
    $title = ($this->userAccount->isAnonymous()) ? 'Log In' : 'Log Out';
    return $title;
  }

  /**
   * Returns the page template and links for navigating log in screens.
   */
  public function customPage() {
    // URLs by route
    $options = array(
      'attributes' => array(
        'class' => array(
          'btn--primary',
        ),
      ),
    );
    $user_login_url = new Url('user.login');
    $cas_login_url = new Url('cas.login', array(), $options);
    $logout_url = new Url('user.logout', array(), $options);

    // Set template variables based on user login status
    if ($this->userAccount->isAnonymous()) {
      $links = array(
        'button' => $this->makeLink->generate('Log in', $cas_login_url),
        'change_login' => $this->makeLink->generate('Visit the administrative access page.', $user_login_url),
      );
    }
    else {
      $links = array(
        'button' => $this->makeLink->generate('Log out', $logout_url),
        'change_login' => $this->makeLink->generate('Visit your user page.', $user_login_url),
      );
    }

    return [
      '#theme' => 'cas_login',
      '#links' => $links,
    ];
  }
}
