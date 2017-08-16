<?php

/**
 * @file
 * Contains \SiteFarmSubContext.
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Drupal\block\Entity\Block;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;
use Drupal\node\Entity\Node;

/**
 * Sub context for SiteFarm step definitions.
 */
class SiteFarmSubContext extends DrupalSubContextBase implements DrupalSubContextInterface {

  /**
   * The Mink context.
   *
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * Contains the name of the currently selected iframe.
   *
   * @var string
   */
  private $iframe = NULL;

  /**
   * An array of Blocks instances created.
   *
   * @var array
   */
  protected $block_instances = array();

  /**
   * Status of default nodes
   *
   * @var boolean
   */
  protected $default_nodes_published = TRUE;

  /**
   * Default nodes created on install
   *
   * @var array
   */
  protected $default_nodes = [
    1 => 'Sample Article',
    2 => 'Event Sample'
  ];

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts(BeforeScenarioScope $scope) {
    $environment = $scope->getEnvironment();

    $this->minkContext = $environment->getContext('Drupal\DrupalExtension\Context\MinkContext');
  }

  /**
   * Get a region by name.
   *
   * @param string $region
   *   The name of the region from the behat.yml file.
   *
   * @return Behat\Mink\Element\Element
   *   An element representing the region.
   */
  public function getRegion($region) {
    $session = $this->getSession();
    $regionObj = $session->getPage()->find('region', $region);
    if (!$regionObj) {
      throw new \Exception(sprintf('No region "%s" found on the page %s.', $region, $session->getCurrentUrl()));
    }
    return $regionObj;
  }

  /**
   * Asserts that an element, specified by CSS selector, exists.
   *
   * @param string $selector
   *   The CSS selector to search for.
   *
   * @Then the element :selector should exist
   */
  public function theElementShouldExist($selector) {
    $this->assertSession()->elementExists('css', $selector);
  }

  /**
   * Clicks an arbitrary element, found by CSS selector.
   *
   * @param string $selector
   *   The CSS selector.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *   If the specified element is not present on the page.
   *
   * @When I click the :selector element
   */
  public function clickElementBySelector($selector) {
    $session = $this->getSession();

    $element = $session->getPage()->find('css', $selector);
    if ($element) {
      try {
        $this->scrollToElement($selector);
      }
      catch (UnsupportedDriverActionException $e) {
        // Don't worry about it.
      }
      $element->click();
    }
    else {
      throw new ElementNotFoundException($session, 'element', 'css', $selector);
    }
  }

  /**
   * Asserts that a role as a set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to check for.
   *
   * @Then the :role role should have permissions:
   * @Then the :role role should have permission to:
   */
  public function assertPermissions($rid, PyStringNode $permissions) {
    foreach ($permissions->getStrings() as $permission) {
      $this->assertPermission($rid, $permission);
    }
  }

  /**
   * Asserts that a role does NOT have a set of permissions.
   *
   * @param string $rid
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to check for.
   *
   * @Then the :role role should not have permissions:
   * @Then the :role role should not have permission to:
   */
  public function assertNoPermissions($rid, PyStringNode $permissions) {
    foreach ($permissions->getStrings() as $permission) {
      $this->assertNoPermission($rid, $permission);
    }
  }

  /**
   * Asserts that a role has a specific permission.
   *
   * @param string $rid
   *   The role ID.
   * @param string $permission
   *   The permission to check for.
   *
   * @Given the :role role has the :permission permission
   * @Given the :role role has permission to :permission
   *
   * @Then the :role role should have the :permission permission
   * @Then the :role role should have permission to :permission
   */
  public function assertPermission($rid, $permission) {
    $this->minkContext->assertAtPath('/admin/people/permissions/' . $rid);
    $this->assertSession()->checkboxChecked($rid . '[' . $permission . ']');
  }

  /**
   * Asserts that a role does not have a specific permission.
   *
   * @param string $rid
   *   The role ID.
   * @param string $permission
   *   The permission to check for.
   *
   * @Given the :role role does not have the :permission permission
   * @Given the :role role does not have permission to :permission
   *
   * @Then the :role role should not have the :permission permission
   * @Then the :role role should not have permission to :permission
   */
  public function assertNoPermission($rid, $permission) {
    $this->minkContext->assertAtPath('/admin/people/permissions/' . $rid);

    $field = $rid . '[' . $permission . ']';
    try {
      $this->assertSession()->fieldNotExists($field);
    }
    catch (ExpectationException $e) {
      $this->assertSession()->checkboxNotChecked($field);
    }
  }

  /**
   * Creates a video entity from an embed code.
   *
   * @param \Behat\Gherkin\Node\PyStringNode $embed_code
   *   The embed code.
   *
   * @Given video from embed code:
   *
   * @When I create a video from embed code:
   */
  public function videoFromEmbedCode(PyStringNode $embed_code) {
    $this->createMediaFromEmbedCode('video', $embed_code, 'field_media_video_embed_field');
  }

  /**
   * Creates a media asset from an embed code.
   *
   * @param string $bundle
   *   The media bundle ID.
   * @param \Behat\Gherkin\Node\PyStringNode $embed_code
   *   The embed code.
   * @param string $field
   *   (optional) The embed code field. Defaults to embed_code.
   *
   * @Given :bundle media from embed code:
   *
   * @When I create :bundle media from embed code:
   */
  public function createMediaFromEmbedCode($bundle, PyStringNode $embed_code, $field = 'embed_code') {
    $entity = \Drupal::entityTypeManager()
      ->getStorage('media')
      ->create([
        'bundle' => $bundle,
        'name' => $this->getRandom()->string(),
        $field => (string) $embed_code,
        'field_media_in_library' => TRUE,
        'status' => TRUE,
      ]);
    $entity->save();

    /** @var \EntitySubContext $context */
    $context = $this->getContext('EntitySubContext');
    $context->queueEntity($entity);
  }

  /**
   * Scrolls an element into the viewport.
   *
   * @param string $selector
   *   The element's CSS selector.
   *
   * @When I scroll to the :selector element
   */
  public function scrollToElement($selector) {
    $this->getSession()
      ->executeScript('document.querySelector("' . addslashes($selector) . '").scrollIntoView()');
  }

  /**
   * Expands a drop button.
   *
   * @param string $selector
   *   The data-drupal-selector attribute of the drop button.
   *
   * @When I expand the :selector drop button
   */
  public function expandDropButton($selector) {
    $selector = 'ul.dropbutton[data-drupal-selector="' . $selector . '"]';
    $this->clickElementBySelector($selector . ' li.dropbutton-toggle');
    $this->scrollToElement($selector);
  }

  /**
   * Waits a while, for debugging.
   *
   * @param int $seconds
   *   How long to wait.
   *
   * @When I wait :seconds seconds
   */
  public function wait($seconds) {
    sleep($seconds);
  }

  /**
   * Maximizes the window (probably for debugging purposes).
   *
   * @When I maximize the window
   */
  public function maximize() {
    $this->getSession()->getDriver()->maximizeWindow();
  }

  /**
   * Takes a screenshot for debugging purposes.
   *
   * @param string $filename
   *   The name of the screenshot file.
   *
   * @When I take a screenshot named :filename
   */
  public function takeScreenshot($filename) {
    $screenshot = $this->getSession()->getDriver()->getScreenshot();
    // If this file is in tests/features/bootstrap, the screenshot be in tests.
    file_put_contents(__DIR__ . '../../' . $filename . '.png', $screenshot);
  }

  /**
   * Fills in an autocompleting entity reference field.
   *
   * @param string $entity_type
   *   The target entity type.
   * @param string $label
   *   The target entity's label.
   * @param string $field
   *   Entity reference field locator.
   *
   * @throws \Exception
   *   If no entities of the given type, with the given label, are found.
   *
   * @When I reference :entity_type :label in :field
   */
  public function referenceEntityByLabel($entity_type, $label, $field) {
    $label_property = \Drupal::entityTypeManager()
      ->getDefinition($entity_type)
      ->getKey('label');

    $entities = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->loadByProperties([
        $label_property => $label,
      ]);

    if ($entities) {
      $this->minkContext->fillField($field, $label . ' (' . reset($entities)->id() . ')');
    }
    else {
      throw new \Exception("No $entity_type labeled '$label' was found.");
    }
  }

  /**
   * Asserts that an image is present and not broken.
   *
   * @Then I should see an image in the :region( region)
   */
  public function assertValidImageRegion($region) {
    $regionObj = $this->getRegion($region);
    $elements = $regionObj->findAll('css', 'img');
    if (empty($elements)) {
      throw new \Exception(sprintf('No image was not found in the "%s" region on the page %s', $region, $this->getSession()->getCurrentUrl()));
    }

    if ($src = $elements[0]->getAttribute('src')) {
      // Make an absolute URL if a relative one is provided
      if (preg_match('/^\//', $src)) {
        global $base_url;
        global $base_path;
        $src = $base_url . $base_path . $src;
      }

      $params = array('http' => array('method' => 'HEAD'));
      $context = stream_context_create($params);
      $fp = @fopen($src, 'rb', FALSE, $context);
      if (!$fp) {
        throw new \Exception(sprintf('Unable to download <img src="%s"> in the "%s" region on the page %s', $src, $region, $this->getSession()->getCurrentUrl()));
      }

      $meta = stream_get_meta_data($fp);
      fclose($fp);
      if ($meta === FALSE) {
        throw new \Exception(sprintf('Error reading from <img src="%s"> in the "%s" region on the page %s', $src, $region, $this->getSession()->getCurrentUrl()));
      }

      $wrapper_data = $meta['wrapper_data'];
      $found = FALSE;
      if (is_array($wrapper_data)) {
        foreach ($wrapper_data as $header) {
          if (substr(strtolower($header), 0, 19) == 'content-type: image') {
            $found = TRUE;
          }
        }
      }

      if (!$found) {
        throw new \Exception(sprintf('Not a valid image <img src="%s"> in the "%s" region on the page %s', $src, $region, $this->getSession()->getCurrentUrl()));
      }
    }
    else {
      throw new \Exception(sprintf('No image had no src="..." attribute in the "%s" region on the page %s', $region, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
   * @Then I should see the image alt :text in the :region( region)
   *
   * NOTE: We specify a regex to allow escaped quotes in the alt text.
   */
  public function assertAltRegion($text, $region) {
    $regionObj = $this->getRegion($region);
    $element = $regionObj->find('css', 'img');
    $tmp = $element->getAttribute('alt');
    if ($text == $tmp) {
      $result = $text;
    }
    if (empty($result)) {
      throw new \Exception(sprintf('No alt text matching "%s" in the "%s" region on the page %s', $text, $region, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
   * Add a block instance into a region based on its Plugin ID
   *
   * @Given a block :plugin_id is in the :region( region)
   */
  public function aBlockIsInTheRegion($plugin_id, $region, array $settings = array()) {
    $this->placeBlock($plugin_id, $region, $settings);
  }

  /**
   * Place a block instance into a region
   *
   * @param $plugin_id
   * @param $region
   * @param array $settings
   */
  public function placeBlock($plugin_id, $region, array $settings = array()) {
    $id = 'behat_block' . rand();

    $this->block_instances[] = $id;

    $config = \Drupal::configFactory();
    $settings += array(
      'plugin' => $plugin_id,
      'region' => $region,
      'id' => $id,
      'theme' => $config->get('system.theme')->get('default'),
      'label' => $id,
      'visibility' => array(),
      'weight' => 0,
    );
    $values = [];
    foreach (array('region', 'id', 'theme', 'plugin', 'weight', 'visibility') as $key) {
      $values[$key] = $settings[$key];
      // Remove extra values that do not belong in the settings array.
      unset($settings[$key]);
    }
    foreach ($values['visibility'] as $id => $visibility) {
      $values['visibility'][$id]['id'] = $id;
    }
    $values['settings'] = $settings;
    $block = Block::create($values);
    $block->save();
    return $block;
  }

  /**
   * Delete any blocks created and placed by the scenario
   *
   * @AfterScenario @api
   */
  public function deleteBlocks($event) {
    foreach ($this->block_instances as $entity_id) {
      Block::load($entity_id)
        ->delete();
    }
  }

  /**
   * @Then the :field select should be set to :value
   */
  public function theSelectShouldBeSetTo($field, $value) {
    $select = $this->getSession()->getPage()->findField($field);
    if (empty($select)) {
      throw new \Exception(sprintf('We couldn\'nt find "%s" on the page', $field));
    }
    $options = $select->findAll('xpath', '//option[@selected="selected"]');
    if (empty($select)) {
      throw new \Exception(sprintf('The select "%s" doesn\'t have any options selected', $field));
    }

    $found = FALSE;
    foreach ($options as $option) {
      if ($option->getText() === $value) {
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      throw new \Exception(sprintf('The select "%s" doesn\'t have the option "%s" selected', $field, $value));
    }
  }

  /**
   * @Then the :field autocomplete field should contain :text
   *
   * Checks, that autocomplete form field with specified id|name|label|value has specified value.
   */
  public function theAutocompleteFieldShouldContain($field, $text)
  {
    $node = $this->assertSession()->fieldExists($field);
    $actual = $node->getValue();

    // Only look for the text at the beginning of the actual value since the
    // actual value will have a node ID appended which we won't know the value of
    if (strpos($text, ',') === FALSE) {
      $regex = '/' . preg_quote($text, '/') . '\s+\(\d+\)/ui';
    }
    else {
      // append the term id regex to the end of each term
      $partials = explode(',', $text);

      foreach ($partials as $key => $partial_text) {
        $partials[$key] = preg_quote(trim($partial_text), '/') . '\s+\(\d+\)';
      }

      $regex = '/' . implode(', ', $partials) . '/ui';
    }

    if (!preg_match($regex, $actual)) {
      throw new \Exception(sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $text));
    }
  }

  /**
   * @Given the Administration Toolbar is hidden
   *
   * We sometimes need to hide the admin toolbar so that buttons can be clicked
   */
  public function theAdministrationToolbarIsHidden()
  {
    $this->getSession()->executeScript('jQuery("#toolbar-administration").hide();');
  }

  /**
   * @Then I( should) see :text in the :tag element in the :region( region)
   */
  public function iShouldSeeTextInTheElementInTheRegion($text, $tag, $region) {
    $regionObj = $this->getRegion($region);
    $elements = $regionObj->findAll('css', $tag);
    if (empty($elements)) {
      throw new \Exception(sprintf('The element "%s" was not found in the "%s" region on the page %s', $tag, $region, $this->getSession()->getCurrentUrl()));
    }

    $found = FALSE;
    foreach ($elements as $element) {
      if ($element->getText() == $text) {
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      throw new \Exception(sprintf('The text "%s" was not found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
   * @When I switch to the frame :frame
   */
  public function iSwitchToTheFrame($frame) {
    $this->getSession()->switchToIFrame($frame);
    $this->iframe = $frame;
  }

  /**
   * @When I switch to the frame with :element element
   */
  public function iSwitchToTheFrameWithElement($element) {
    // Get the frame ID from the given element
    $container = $this->getSession()->getPage();
    $node = $container->find('css', $element);

    if (null === $node) {
      if (is_array($element)) {
        $element = implode(' ', $element);
      }

      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $element);
    }

    $frame = $node->getAttribute('id');

    $this->getSession()->switchToIFrame($frame);
    $this->iframe = $frame;
  }

  /**
   * @When I switch out of all frames
   */
  public function iSwitchOutOfAllFrames() {
    $this->getSession()->switchToIFrame();
    $this->iframe = NULL;
  }

  /**
   * @Given I delete the most recent custom block
   */
  public function iDeleteTheMostRecentCustomBlock() {
    // Fetch the id of the last block created
    $block_id = \Drupal::database()->select('block_content', 'b')
      ->fields('b', ['id'])
      ->orderBy('b.id', 'DESC')
      ->range(0, 1)
      ->execute()->fetchField();

    // Go to the block delete page
    $path = 'block/' . $block_id . '/delete';
    $this->minkContext->assertAtPath($path);
    $this->minkContext->pressButton('Delete');
  }

  /**
   * @Given default nodes are unpublished
   */
  public function defaultNodesAreUnpublished() {
    $this->default_nodes_published = FALSE;

    foreach ($this->default_nodes as $nid => $title) {
      $node = Node::load($nid);

      if ($node && $node->getTitle() == $title) {
        $node->status = 0;
        $node->save();
      }

    }
  }

  /**
   * Re-publish any default nodes
   *
   * @AfterScenario
   */
  public function republishDefaultNodes() {
    $this->default_nodes_published = TRUE;

    foreach ($this->default_nodes as $nid => $title) {
      $node = Node::load($nid);

      if ($node && $node->getTitle() == $title) {
        $node->status = 1;
        $node->save();
      }
    }
  }

}
