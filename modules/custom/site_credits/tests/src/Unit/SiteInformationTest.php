<?php

namespace Drupal\Tests\site_credits\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\site_credits\SiteInformation;

class SiteInformationTest extends UnitTestCase
{

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * Form State stub.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $FormState;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp()
  {
    parent::setUP();

    $this->configFactory = $this->getMock('Drupal\Core\Config\ConfigFactoryInterface');

    $value = 'new info';
    $config = $this->getMockBuilder('Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->once())
      ->method('set')
      ->with('credit_info', $value)
      ->will($this->returnValue($config));
    $config->expects($this->once())
      ->method('save')
      ->will($this->returnValue($config));
    $this->configFactory->expects($this->once())
      ->method('getEditable')
      ->with('system.site')
      ->will($this->returnValue($config));

    $this->FormState = $this->getMock('Drupal\Core\Form\FormStateInterface');
    $this->FormState->expects($this->any())
      ->method('getValue')
      ->willReturn($value);
  }

  /**
   * Test the submitForm() method
   */
  public function testSubmitForm()
  {
    $form = array();

    $info = new SiteInformation($this->configFactory);

    $info->submitForm($form, $this->FormState);
  }

}
