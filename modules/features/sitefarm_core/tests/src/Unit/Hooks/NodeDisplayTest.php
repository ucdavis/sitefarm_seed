<?php

namespace Drupal\Tests\sitefarm_core\Unit\Hooks;

use Drupal\Tests\UnitTestCase;
use Drupal\sitefarm_core\Hooks\NodeDisplay;
use Drupal\node\Entity\Node;

/**
 * @coversDefaultClass \Drupal\sitefarm_core\Hooks\NodeDisplay
 * @group sitefarm_core_hooks
 */
class NodeDisplayTest extends UnitTestCase {

  /**
   * @var \Drupal\sitefarm_core\Hooks\NodeDisplay
   */
  protected $helper;

  /**
   * Create the setup for constants
   */
  protected function setUp() {
    parent::setUp();

    $this->helper = new NodeDisplay();
  }

  /**
   * Tests forceRestrictedHtmlOnTeasers method
   *
   * @dataProvider forceRestrictedHtmlOnTeasersProvider
   */
  public function testForceRestrictedHtmlOnTeasers($build, $view_mode, $expected) {
    $this->helper->forceRestrictedHtmlOnTeasers($build, $view_mode);
    $this->assertArrayEquals($expected, $build);
  }

  /**
   * Provider for testForceRestrictedHtmlOnTeasers()
   */
  public function forceRestrictedHtmlOnTeasersProvider() {
    return [
      [
        ['test'],
        'teaser',
        ['test']
      ],
      [
        ['test'],
        'full',
        ['test']
      ],
      [
        ['body' => 'test'],
        'teaser',
        ['body' => 'test'],
      ],
      [
        ['body' => [
          '#formatter' => 'text_summary_or_trimmed'
        ]],
        'full',
        ['body' => [
          '#formatter' => 'text_summary_or_trimmed'
        ]]
      ],
      [
        ['body' => [
          '#formatter' => 'test'
        ]],
        'full',
        ['body' => [
          '#formatter' => 'test'
        ]]
      ],
      [
        ['body' => [
          '#formatter' => 'test'
        ]],
        'teaser',
        ['body' => [
          '#formatter' => 'test'
        ]]
      ],
      [
        ['body' => [
          '#formatter' => 'text_summary_or_trimmed'
        ]],
        'teaser',
        ['body' => [
          0 => ['#format' => 'sf_restricted_html'],
          '#formatter' => 'text_summary_or_trimmed'
        ]]
      ],
    ];
  }

  /**
   * Tests forcePlainTextOnPoster method
   *
   * @dataProvider forcePlainTextOnPosterProvider
   */
  public function testForcePlainTextOnPoster($build, $view_mode, $expected) {
    $this->helper->forcePlainTextOnPoster($build, $view_mode);
    $this->assertArrayEquals($expected, $build);
  }

  /**
   * Provider for testForcePlainTextOnPoster()
   */
  public function forcePlainTextOnPosterProvider() {
    return [
      [
        ['test'],
        'teaser',
        ['test']
      ],
      [
        ['test'],
        'poster',
        ['test']
      ],
      [
        ['body' => ['test']],
        'poster',
        ['body' => ['test']],
      ],
      [
        ['body' => [
          ['#test' => 'text_summary_or_trimmed']
        ]],
        'poster',
        ['body' => [
          ['#test' => 'text_summary_or_trimmed']
        ]]
      ],
      [
        ['body' => [
          ['#format' => 'text_summary_or_trimmed']
        ]],
        'teaser',
        ['body' => [
          ['#format' => 'text_summary_or_trimmed']
        ]]
      ],
      [
        ['body' => [
          [
            '#format' => 'text_summary_or_trimmed',
            '#text' => 'text'
          ]
        ]],
        'poster',
        ['body' => [
          [
            '#format' => 'sf_plain_text',
            '#text' => 'text'
          ]
        ]],
      ],
      [
        ['body' => [
          [
            '#format' => 'text_summary_or_trimmed',
            '#text' => '<div>text</div>'
          ]
        ]],
        'poster',
        ['body' => [
          [
            '#format' => 'sf_plain_text',
            '#text' => 'text'
          ]
        ]],
      ],
    ];
  }

  /**
   * Tests addFeaturedStatus method
   *
   * @dataProvider addFeaturedStatusProvider
   */
  public function testAddFeaturedStatus($featured_status, $view_mode, $expected) {
    $variables = [];
    $variables['view_mode'] = $view_mode;

    // Mock the magic public property chain $node->field_sf_featured_status->value;
    $value_class = new \stdClass();
    $value_class->value = $featured_status;

    $node = $this->prophesize(Node::CLASS);
    $test_node = $node->reveal();
    if ($featured_status) {
      $test_node->field_sf_featured_status = $value_class;
    }
    $variables['node'] = $test_node;

    $this->helper->addFeaturedStatus($variables);

    // Set up a return variable that looks for the newly set featured status
    $return = (isset($variables['featured_status'])) ? $variables['featured_status'] : NULL;

    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testAddFeaturedStatus()
   */
  public function addFeaturedStatusProvider() {
    return [
      [
        1,
        'teaser',
        1
      ],
      [
        1,
        'full',
        NULL
      ],
      [
        0,
        'full',
        NULL
      ],
      [
        0,
        'teaser',
        0
      ],
      [
        NULL,
        'teaser',
        NULL
      ],
    ];
  }
}
