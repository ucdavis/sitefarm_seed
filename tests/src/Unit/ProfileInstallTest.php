<?php

namespace Drupal\Tests\sitefarm_seed\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_seed\ProfileInstall;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @coversDefaultClass \Drupal\sitefarm_seed\ProfileInstall
 * @group sitefarm_seed
 */
class ProfileInstallTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\sitefarm_seed\ProfileInstall
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->entityTypeManager = $this->prophesize(EntityTypeManagerInterface::CLASS);

    $this->helper = new ProfileInstall($this->entityTypeManager->reveal());
  }

  /**
   * Tests the hideAndSetDefaultRegion() method
   */
  public function testHideAndSetDefaultRegion() {
    $form = [
      'regional_settings' => [
        '#type' => 'visible',
        'site_default_country' => ['#default_value' => 'country'],
        'date_default_timezone' => ['#default_value' => 'timezone'],
      ]
    ];

    $expected = [
      'regional_settings' => [
        '#type' => 'hidden',
        'site_default_country' => ['#default_value' => 'US'],
        'date_default_timezone' => ['#default_value' => 'America/Los_Angeles'],
      ]
    ];

    $this->helper->hideAndSetDefaultRegion($form);
    $this->assertArrayEquals($expected, $form);
  }

  /**
   * Tests the removeUpdateNotificationOptions() method
   */
  public function testRemoveUpdateNotificationOptions() {
    $form = [
      'update_notifications' => ''
    ];

    $this->helper->removeUpdateNotificationOptions($form);
    $this->assertEmpty($form);
  }

  /**
   * Tests the useMailInContactForm() method
   */
  public function testUseMailInContactForm() {
    $form = [];
    $this->helper->useMailInContactForm($form);
    $this->assertInstanceOf('Drupal\sitefarm_seed\ProfileInstall', $form['#submit'][0][0]);
    $this->assertEquals('useMailInContactFormSubmit', $form['#submit'][0][1]);
  }

}
