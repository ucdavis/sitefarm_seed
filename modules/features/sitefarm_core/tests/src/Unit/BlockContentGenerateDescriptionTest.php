<?php

namespace Drupal\Tests\sitefarm_core\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\BlockContentGenerateDescription;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Prophecy\Argument;
use Drupal\block\BlockForm;
use Drupal\block_content\Entity\BlockContent;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\BlockContentGenerateDescription
 * @group sitefarm_core
 */
class BlockContentGenerateDescriptionTest extends UnitTestCase
{
  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The database query statment.
   *
   * @var \Drupal\Core\Database\StatementInterface
   */
  protected $statement;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * @var \Drupal\sitefarm_core\BlockContentGenerateDescription
   */
  protected $helperObj;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->configFactory = $this->getConfigFactoryStub(
      [
        'sitefarm_core.settings' => [
          'generate_custom_block_title' => 1
        ]
      ]
    );

    $this->statement = $this->prophesize(StatementInterface::CLASS);
    $this->statement->fetchField()->willReturn(FALSE);

    $query = $this->prophesize(SelectInterface::CLASS);
    $query->addField('bc', 'info')->willReturn(NULL);
    $query->condition('bc.info', Argument::any())->willReturn(NULL);
    $query->countQuery()->willReturn($query->reveal());
    $query->execute()->willReturn($this->statement->reveal());

    $this->database = $this->prophesize(Connection::CLASS);
    $this->database->select('block_content_field_data', 'bc')->willReturn($query->reveal());

    $this->entityTypeBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::CLASS);

    $this->helperObj = new BlockContentGenerateDescription($this->database->reveal(), $this->entityTypeBundleInfo->reveal(), $this->configFactory);
  }

  /**
   * Tests the createFromTitle method
   */
  public function testCreateFromTitle() {
    $form = ['field_sf_title' => 'test'];
    $form['info']['widget'][0]['value']['#default_value'] = '';

    $this->helperObj->createFromTitle($form);

    $this->assertInstanceOf('Drupal\sitefarm_core\BlockContentGenerateDescription', $form['#validate'][0][0]);
    $this->assertEquals('createDescription', $form['#validate'][0][1]);
    $this->assertEquals('hidden', $form['info']['widget'][0]['value']['#type']);
    $this->assertEquals('block_title_placeholder', $form['info']['widget'][0]['value']['#default_value']);
  }

  /**
   * Tests the createFromTitle method
   */
  public function testCreateFromTitleIsDisabled() {
    $this->configFactory = $this->getConfigFactoryStub(
      [
        'sitefarm_core.settings' => [
          'generate_custom_block_title' => 0
        ]
      ]
    );

    $generator = new BlockContentGenerateDescription($this->database->reveal(), $this->entityTypeBundleInfo->reveal(), $this->configFactory);

    $form = ['field_sf_title' => 'test'];

    $generator->createFromTitle($form);

    $expected = ['field_sf_title' => 'test'];

    $this->assertArrayEquals($expected, $form);
  }


  /**
   * Test for the createDescription method.
   */
  public function testCreateDescription() {
    $form = [];
    $form_state_stub = [];

    $blockContent = $this->prophesize(BlockContent::CLASS);
    $blockContent->bundle()->willReturn('test_bundle');

    $blockForm = $this->prophesize(BlockForm::CLASS);
    $blockForm->getEntity()->willReturn($blockContent->reveal());

    // Mock the formState
    $form_state = $this->getMock(FormStateInterface::CLASS);
    $form_state->expects($this->any())
      ->method('getFormObject')
      ->willReturn($blockForm->reveal());
    $form_state->expects($this->any())
      ->method('getValue')
      ->willReturn([['value' => 'Block Content Title']]);
    $form_state->expects($this->any())
      ->method('setValue')
      ->willReturnCallback(function ($key, $value) use (&$form_state_stub) {
        $form_state_stub[$key] = $value;
      });

    $this->entityTypeBundleInfo->getBundleInfo('block_content')->willReturn([
      'test_bundle' => [
        'label' => 'Test Bundle Label'
      ]
    ]);

    $this->helperObj->createDescription($form, $form_state);
    $this->assertArrayEquals(['info' => [['value' => 'TBL: Block Content Title']]], $form_state_stub);
  }

  /**
   * Tests the generateUnique Method
   *
   * @param string $text
   * @param int $existing_levels
   *   Number of levels that an existing description is found
   * @param string $expected
   *
   * @dataProvider generateUniqueProvider
   */
  public function testGenerateUnique($text, $existing_levels, $expected) {
    // Reset the unique counter
    $count = 0;
    $this->statement->fetchField()->will(function () use (&$count, $existing_levels) {
      if ($count < $existing_levels) {
        $count++;
        return TRUE;
      } else {
        return False;
      }
    });

    $return = $this->helperObj->generateUnique($text);

    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testGenerateUnique()
   */
  public function generateUniqueProvider() {
    return [
      ['New Description', 0, 'New Description'],
      ['An Existing Description', 1, 'An Existing Description 1'],
      ['An Existing Description', 2, 'An Existing Description 2'],
      ['An Existing Description', 3, 'An Existing Description 3'],
      ['An Existing Description 1', 1, 'An Existing Description 2'],
      ['New Description 3', 0, 'New Description 3'],
    ];
  }

  /**
   * Tests the createPrefix method
   *
   * @dataProvider createPrefixProvider
   */
  public function testCreatePrefix($text, $expected) {
    $return = $this->helperObj->createPrefix($text);
    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testCreatePrefix()
   */
  public function createPrefixProvider() {
    return [
      ['Sample Text', 'ST'],
      ['sample text', 'ST'],
      ['Sample text', 'ST'],
      ['Sample Text Here', 'STH'],
      ['singleword', 'S'],
      ['Wor"d wi*&th Lot\'s of Punctuation.!@', 'WWLOP'],
      ['This   Is   A   []  Word!', 'TIAW'],
    ];
  }

}
