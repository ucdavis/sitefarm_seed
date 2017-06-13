<?php

namespace Drupal\Tests\sitefarm_simple_configuration\Unit\Form;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_simple_configuration\Form\CacheClearForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * @coversDefaultClass \Drupal\sitefarm_simple_configuration\Form\CacheClearForm
 * @group sitefarm_simple_configuration
 */
class CacheClearFormTest extends UnitTestCase {

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Form State stub.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\sitefarm_simple_configuration\Form\CacheClearForm
   */
  protected $formClass;

  /**
   * Create the setup of the $formClass to test against.
   */
  protected function setUp()
  {
    parent::setUp();

    // Mock the configFactory
    $this->configFactory = $this->prophesize(ConfigFactoryInterface::CLASS);

    // Mock the formState
    $this->formState = $this->getMock(FormStateInterface::CLASS);

    // Create the form Class to test against
    $this->formClass = new CacheClearForm($this->configFactory->reveal());

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->formClass->setStringTranslation($translator);
  }

  /**
   * Tests the getFormId method.
   *
   * @see ::getFormId()
   */
  public function testGetFormId() {
    $this->assertEquals('cache_clear_form', $this->formClass->getFormId());
  }

  /**
   * Tests the buildForm method.
   *
   * @see ::buildForm()
   */
  public function testBuildForm() {
    $form = [];
    $result = $this->formClass->buildForm($form, $this->formState);

    $this->assertEquals('markup', $result['clear_all_caches']['#type']);
    $this->assertEquals('Clear all caches', $result['actions']['submit']['#value']);
  }

  /**
   * Tests the validateForm method.
   *
   * @see ::validateForm()
   */
  public function testValidateForm() {
    $form = [];
    $result = $this->formClass->validateForm($form, $this->formState);

    $this->assertNull($result);
  }

  /**
   * Tests the submitForm method.
   *
   * @see ::submitForm()
   */
  public function testSubmitForm() {
    $formClass = new MockCacheClearForm($this->configFactory->reveal());

    $form = [];
    $result = $formClass->submitForm($form, $this->formState);

    $this->assertNull($result);
  }

}
