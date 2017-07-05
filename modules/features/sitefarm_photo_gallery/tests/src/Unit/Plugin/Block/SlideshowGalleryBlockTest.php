<?php

namespace Drupal\Tests\sitefarm_photo_gallery\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_photo_gallery\Plugin\Block\SlideshowGalleryBlock;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\Renderer;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Plugin\Field\FieldType\FileFieldItemList;
use Drupal\file\Entity\File;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\sitefarm_photo_gallery\Plugin\Block\SlideshowGalleryBlock
 * @group sitefarm_photo_gallery
 */
class SlideshowGalleryBlockTest extends UnitTestCase
{

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Form State Mock stub.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * Default config for the block plugin
   *
   * @var array
   */
  protected $pluginConfig = array(
    'show_title' => TRUE,
    'lazy_load' => TRUE,
    'slider_nav' => FALSE,
  );

  /**
   * @var \Drupal\sitefarm_photo_gallery\Plugin\Block\SlideshowGalleryBlock
   */
  protected $plugin;

  /**
   * Create the setup for constants and configFactory stub
   */
  protected function setUp() {
    parent::setUp();

    // Stub config
    $this->configFactory = $this->getConfigFactoryStub(array(
      'gallery' => []
    ));

    // Stub the Entity Type Manager
    $this->entityTypeManager = $this->prophesize(EntityTypeManagerInterface::CLASS);

    // Stub the Renderer
    $this->renderer = $this->prophesize(Renderer::CLASS);

    // stub form_state
    $this->formState = $this->getMock(FormStateInterface::CLASS);

    $plugin_id = 'sitefarm_photo_gallery_block';
    $plugin_definition['provider'] = 'sitefarm_photo_gallery';

    $this->plugin = new SlideshowGalleryBlock(
      $this->pluginConfig,
      $plugin_id,
      $plugin_definition,
      $this->configFactory,
      $this->entityTypeManager->reveal(),
      $this->renderer->reveal()
    );

    // Create a translation stub for the t() method
    $translator = $this->getStringTranslationStub();
    $this->plugin->setStringTranslation($translator);
  }

  /**
   * Tests the create method.
   */
  public function testCreate() {
    $plugin_id = 'sitefarm_photo_gallery_block';
    $plugin_definition['provider'] = 'sitefarm_photo_gallery';

    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('config.factory')->willReturn($this->configFactory);
    $container->get('entity_type.manager')->willReturn($this->entityTypeManager);
    $container->get('renderer')->willReturn($this->renderer);

    $instance = SlideshowGalleryBlock::create($container->reveal(), $this->pluginConfig, $plugin_id, $plugin_definition);
    $this->assertInstanceOf('Drupal\sitefarm_photo_gallery\Plugin\Block\SlideshowGalleryBlock', $instance);
  }

  /**
   * Tests the blockSubmit method.
   */
  public function testBlockSubmit() {
    $options = array(
      'display' => $this->pluginConfig
    );

    $this->formState->expects($this->any())
      ->method('getValues')
      ->willReturn($options);
    $this->formState->expects($this->any())
      ->method('getValue')
      ->willReturnCallback(function ($array) use ($options) {
        return $options[$array[0]][$array[1]];
      });

    $expectedConfig = $this->pluginConfig + array(
        'id' => 'sitefarm_photo_gallery_block',
        'label' => '',
        'provider' => 'sitefarm_photo_gallery',
        'label_display' => 'visible',
      );

    $form = [];
    $this->plugin->blockSubmit($form, $this->formState);
    $this->assertArrayEquals($expectedConfig, $this->plugin->getConfiguration());
  }

  /**
   * Tests the build method.
   */
  public function testBuild() {
    $config = array_merge($this->pluginConfig, ['gallery' => 1, 'slider_nav' => 1]);
    $this->plugin->setConfiguration($config);

    $field_props = [
      [
        'target_id' => '1',
        'alt' => 'alt text',
        'title' => '',
        'width' => '720',
        'height' => '480',
      ],
    ];

    $file = $this->prophesize(File::CLASS);
    $file->getFileUri()->willReturn('fileuri');

    $slides = [
      $file->reveal(),
    ];

    $field = $this->prophesize(FileFieldItemList::CLASS);
    $field->getValue()->willReturn($field_props);
    $field->referencedEntities()->willReturn($slides);

    $node = $this->prophesize(Node::CLASS);
    $node->getTitle()->willReturn('Test Title');
    $node_rendered = $node->reveal();

    // Use reflection to alter the protected $node->fieldDefinitions
    $reflectionObject = new \ReflectionObject($node_rendered);
    $property = $reflectionObject->getProperty('fieldDefinitions');
    $property->setAccessible(true);
    $property->setValue($node_rendered, []);
    // Use reflection to alter the protected $node->values
    $property = $reflectionObject->getProperty('values');
    $property->setAccessible(true);
    $property->setValue($node_rendered, ['field_sf_gallery_photos' => $field->reveal()]);

    $image_style = $this->prophesize(ImageStyle::CLASS);
    $image_style->buildUrl('fileuri')->willReturn('image_src');
    $image_style_thumb = $this->prophesize(ImageStyle::CLASS);
    $image_style_thumb->buildUrl('fileuri')->willReturn('thumb_src');

    $entityStorage = $this->prophesize(EntityStorageInterface::CLASS);
    $entityStorage->load(1)->willReturn($node_rendered);
    $entityStorage->load('sf_slideshow_full')->willReturn($image_style->reveal());
    $entityStorage->load('sf_slideshow_thumbnail')->willReturn($image_style_thumb->reveal());

    $this->entityTypeManager->getStorage('node')->willReturn($entityStorage->reveal());
    $this->entityTypeManager->getStorage('image_style')->willReturn($entityStorage->reveal());

    $expectedBuild = [
      '#attached' => array(
        'library' =>  array(
          'sitefarm_photo_gallery/sitefarm_photo_gallery',
          'sitefarm_photo_gallery/sitefarm_photo_gallery.slick',
          'sitefarm_photo_gallery/sitefarm_photo_gallery.slick_theme',
        ),
      ),
      'slideshow_main' => [
        '#theme' => 'slideshow_main',
        '#show_title' => TRUE,
        '#title_start' => 'Test',
        '#title_end' => ' Title',
        '#title_full' => 'Test Title',
        '#lazy_load' => TRUE,
        '#slides' => [
          [
            'image' => [
              '#theme' => 'image_style',
              '#width' => '720',
              '#height' => '480',
              '#style_name' => 'sf_slideshow_full',
              '#alt' => 'alt text',
              '#uri' => 'fileuri',
            ],
            'src' => 'image_src',
            'alt' => 'alt text',
            'caption' => '',
          ],
        ],
      ],
      'slideshow_nav' => [
        '#theme' => 'slideshow_nav',
        '#lazy_load' => TRUE,
        '#slides' => [
          [
            'image' => [
              '#theme' => 'image_style',
              '#width' => '720',
              '#height' => '480',
              '#style_name' => 'sf_slideshow_thumbnail',
              '#alt' => 'alt text',
              '#uri' => 'fileuri',
            ],
            'src' => 'thumb_src',
            'alt' => 'alt text',
          ]
        ],
      ]
    ];

    $return = $this->plugin->build();
    $this->assertEquals($expectedBuild, $return);
  }

  /**
   * Tests the build method does not have a valid node.
   *
   * @covers ::build()
   */
  public function testBuildDoesNotHaveAValidNode() {
    $this->plugin->setConfiguration($this->pluginConfig + ['gallery' => 1]);

    $entityStorage = $this->prophesize(EntityStorageInterface::CLASS);
    $entityStorage->load(1)->willReturn(NULL);

    $this->entityTypeManager->getStorage('node')->willReturn($entityStorage->reveal());

    $this->assertEmpty($this->plugin->build());
  }

  /**
   * Tests the getCacheTags method.
   */
  public function testGetCacheTags() {
    // If a gallery node is not set
    $this->assertArrayEquals([], $this->plugin->getCacheTags());

    // If gallery node is set
    $this->plugin->setConfiguration(['gallery' => 1]);
    $this->assertArrayEquals(['node:1'], $this->plugin->getCacheTags());
  }

  /**
   * Tests blockForm().
   */
  public function testBlockForm() {
    $form = [];
    $return = $this->plugin->blockForm($form, $this->formState);
    $this->assertEquals('entity_autocomplete', $return['display']['gallery']['#type']);
    $this->assertEmpty($return['display']['gallery']['#default_value']);
    $this->assertTrue($return['display']['show_title']['#default_value']);
    $this->assertTrue($return['display']['lazy_load']['#default_value']);
    $this->assertFalse($return['display']['slider_nav']['#default_value']);
  }

  /**
   * Tests that the blockForm has a default gallery node selected.
   *
   * @covers ::blockForm()
   */
  public function testBlockFormHasDefaultGallery() {
    $form = [];

    // A default node is set
    $this->plugin->setConfiguration(['gallery' => 1]);

    $node = $this->prophesize(NodeInterface::CLASS);

    $entityStorage = $this->prophesize(EntityStorageInterface::CLASS);
    $entityStorage->load(1)->willReturn($node->reveal());

    $this->entityTypeManager->getStorage('node')->willReturn($entityStorage->reveal());

    $return = $this->plugin->blockForm($form, $this->formState);
    $this->assertInstanceOf('Drupal\node\NodeInterface', $return['display']['gallery']['#default_value']);
  }
}
