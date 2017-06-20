<?php

namespace Drupal\Tests\sitefarm_core\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\BlockConfigFormHelpers;
use Drupal\Core\Form\FormStateInterface;
use Drupal\block\BlockForm;
use Drupal\block\Entity\Block;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\block_content\Entity\BlockContent;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\BlockConfigFormHelpers
 * @group sitefarm_core
 */
class BlockConfigFormHelpersTest extends UnitTestCase
{

  /**
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * @var \Drupal\sitefarm_core\BlockConfigFormHelpers
   */
  protected $helperObj;

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @var \Drupal\block\Entity\Block
   */
  protected $block;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $plugin = $this->prophesize(BlockPluginInterface::CLASS);
    $plugin->getBaseId()->willReturn('block_content');
    $plugin->getDerivativeId()->willReturn('uuid');

    $this->block = $this->prophesize(Block::CLASS);
    $this->block->getPlugin()->willReturn($plugin->reveal());
    $this->block->id()->willReturn(1);

    $blockForm = $this->prophesize(BlockForm::CLASS);
    $blockForm->getEntity()->willReturn($this->block->reveal());

    $this->formState = $this->prophesize(FormStateInterface::CLASS);
    $this->formState->getFormObject()->willReturn($blockForm->reveal());

    $blockContent = $this->prophesize(BlockContent::CLASS);
    $blockContent->bundle()->willReturn('test_bundle');

    $this->entityRepository = $this->prophesize(EntityRepositoryInterface::CLASS);
    $this->entityRepository->loadEntityByUuid('block_content', 'uuid')->willReturn($blockContent->reveal());

    $this->helperObj = new BlockConfigFormHelpers($this->entityRepository->reveal());

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->helperObj->setStringTranslation($translator);
  }

  /**
   * Tests the getBlockContentBundle method
   */
  public function testGetBlockContentBundle() {
    $return = $this->helperObj->getBlockContentBundle($this->formState->reveal());
    $this->assertEquals('test_bundle', $return);

    // no entity returned
    $this->entityRepository->loadEntityByUuid('block_content', 'uuid')->willReturn(NULL);
    $return = $this->helperObj->getBlockContentBundle($this->formState->reveal());
    $this->assertFalse($return);
  }

  /**
   * Tests the getBlockConfigEntity method
   */
  public function testGetBlockConfigEntity() {
    $return = $this->helperObj->getBlockConfigEntity($this->formState->reveal());
    $this->assertInstanceOf('Drupal\block\Entity\Block', $return);
  }

  /**
   * Tests the getBlockEntityPlugin method
   */
  public function testGetBlockEntityPlugin() {
    $return = $this->helperObj->getBlockEntityPlugin($this->formState->reveal());
    $this->assertInstanceOf('Drupal\Core\Block\BlockPluginInterface', $return);
  }

  /**
   * Tests the getBlockContentEntity method
   */
  public function testGetBlockContentEntity() {
    $return = $this->helperObj->getBlockContentEntity($this->formState->reveal());
    $this->assertInstanceOf('Drupal\block_content\Entity\BlockContent', $return);

    // Cause an entity not to be found
    $this->entityRepository->loadEntityByUuid('block_content', 'uuid')->willReturn(NULL);
    $return = $this->helperObj->getBlockContentEntity($this->formState->reveal());
    $this->assertFalse($return);
  }

  /**
   * Tests the hideBlockTitleCheckbox method
   */
  public function testHideBlockTitleCheckbox() {
    $expected = [
      'settings' => [
        'label_display' => [
          '#prefix' => 'The Block Title will not be displayed.',
          '#type' => 'hidden',
          '#default_value' => FALSE,
        ]
      ]
    ];

    $form = [];
    $this->helperObj->hideBlockTitleCheckbox($form);
    $this->assertArrayEquals($expected, $form);
  }

  /**
   * Tests the unCheckBlockTitle method
   */
  public function testUnCheckBlockTitle() {
    $form = [];
    $this->helperObj->unCheckBlockTitle($form, $this->formState->reveal());
    $this->assertEmpty($form);

    // Set default to FALSE if there is no block id
    $this->block->id()->willReturn(NULL);
    $this->helperObj->unCheckBlockTitle($form, $this->formState->reveal());
    $this->assertFalse($form['settings']['label_display']['#default_value']);
  }

}
