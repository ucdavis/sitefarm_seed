<?php

/**
 * @file
 * Contains \CkEditorSubContext.
 */

use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Contains steps for working with CKEditor instances.
 */
class CkEditorSubContext extends DrupalSubContextBase implements DrupalSubContextInterface {

  /**
   * The Mink context.
   *
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

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
   * Asserts that a CKEditor instance exists.
   *
   * @param string $id
   *   The editor instance ID.
   *
   * @throws \Exception
   *   If the specified CKEditor instance does not exist.
   *
   * @Given CKEditor :id exists
   *
   * @Then CKEditor :id should exist
   */
  public function assertEditorExists($id) {
    $exists = $this->getSession()
      ->evaluateScript("CKEDITOR.instances.hasOwnProperty('$id');");

    if ($exists == FALSE) {
      throw new \Exception("CKEditor '$id' does not exist.");
    }
  }

  /**
   * Puts text or HTML into a CKEditor instance.
   *
   * @param string $text
   *   The text (or HTML) to insert into the editor.
   * @param string $id
   *   (optional) The editor instance ID.
   *
   * @When I put :text into CKEditor
   * @When I put :text into CKEditor :id
   */
  public function insert($text, $id = NULL) {
    $id = $id ?: $this->getDefaultEditorId();

    $this->getSession()
      ->executeScript("CKEDITOR.instances['$id'].insertHtml('$text');");
  }

  /**
   * Asserts that a CKEditor's content contains a snippet of text.
   *
   * @param string $text
   *   The text (or HTML) snippet to look for.
   * @param string $id
   *   (optional) The editor instance ID.
   *
   * @throws \Exception
   *   If the editor does not contain the specified text.
   *
   * @Then CKEditor should contain :text
   * @Then CKEditor :id should contain :text
   */
  public function assertEditorContains($text, $id = NULL) {
    $id = $id ?: $this->getDefaultEditorId();

    if (strpos($this->getContent($id), $text) == FALSE) {
      throw new \Exception("CKEditor $id did not contain '$text'.");
    }
  }

  /**
   * Assert that a CKEditor's content matches a regular expression.
   *
   * @param string $expression
   *   The regular expression to match.
   * @param string $id
   *   (optional) The editor instance ID.
   *
   * @throws \Exception
   *   If the expression does not match.
   *
   * @Then CKEditor should match :expression
   * @Then CKEditor :id should match :expression
   */
  public function assertEditorMatch($expression, $id = NULL) {
    $id = $id ?: $this->getDefaultEditorId();

    if (preg_match($expression, $this->getContent($id)) == 0) {
      throw new \Exception("CKEditor $id did not match '$expression'.");
    }
  }

  /**
   * Gets the content of a CKEditor instance.
   *
   * @param string $id
   *   The editor instance ID.
   *
   * @return string
   *   The HTML content of the editor.
   */
  protected function getContent($id) {
    return $this->getSession()
      ->evaluateScript("CKEDITOR.instances['$id'].getData();");
  }

  /**
   * Executes a CKEditor command.
   *
   * @param string $command
   *   The command ID, as known to CKEditor's API.
   * @param string $id
   *   (optional) The editor instance ID.
   * @param mixed $data
   *   Additional data to pass to the executed command.
   *
   * @When I execute the :command command in CKEditor
   * @When I execute the :command command in CKEditor :id
   */
  public function execute($command, $id = NULL, $data = NULL) {
    $id = $id ?: $this->getDefaultEditorId();

    $data = json_encode($data);
    $this->getSession()
      ->evaluateScript("CKEDITOR.instances['$id'].execCommand('$command', $data);");
  }

  /**
   * Executes a CKEditor command multiple times.
   *
   * @param string $command
   *   The command ID, as known to CKEditor's API.
   * @param string $id
   *   (optional) The editor instance ID.
   * @param mixed $data
   *   Additional data to pass to the executed command.
   *
   * @When I execute the :command command in CKEditor :count times
   * @When I execute the :command command in CKEditor :id :count times
   */
  public function executeMultiple($command, $count = 1, $id = NULL, $data = NULL) {
    for ($i = 1; $i <= $count; $i++) {
      $this->execute($command, $id, $data);
    }
  }

  /**
   * Returns the first available CKEditor instance ID.
   *
   * @return string|false
   *   The first CKEditor instance ID, or FALSE if there are no instances.
   */
  protected function getDefaultEditorId() {
    $instances = $this->getInstances();
    return reset($instances);
  }

  /**
   * Returns a list of all CKEditor instance IDs.
   *
   * @return string[]
   *   The CKEditor instance IDs.
   */
  protected function getInstances() {
    $instances = $this->getSession()
      ->evaluateScript('Object.keys(CKEDITOR.instances).join(",")');

    return explode(',', $instances);
  }

  /**
   * @When I click :text in the CKEditor style panel
   */
  public function iClickInTheCKEditorStylePanel($text) {
    $session = $this->getSession();

    // Make sure any ajax is finished
    $this->minkContext->iWaitForAjaxToFinish();

    // Click the Style Button
    $style_button = ".cke_combo_button";

    $element = $session->getPage()->find('css', $style_button);
    if ($element) {
      $element->click();
    }
    else {
      throw new ElementNotFoundException($session, 'element', 'css', $style_button);
    }

    // Switch to the dropdown panel iframe

    // Get the iframe ID from class name
    $iframe_class = '.cke_panel_frame';
    $element = $session->getPage()->find('css', $iframe_class);

    if (null === $element) {
      throw new ElementNotFoundException($session->getDriver(), 'element', 'css', $iframe_class);
    }

    // Get the iframe ID
    $frame_id = $element->getAttribute('id');

    // Switch to the Panel iframe
    $session->switchToIFrame($frame_id);

    // Scroll to the style selector that will be clicked
    $selector = $session->getPage()->findLink($text);
    $this->scrollToTextInElement($selector);

    // Click the Style link
    $this->minkContext->clickLink($text);

    // Switch out of the panel iframe back to the main page
    $session->switchToIFrame();
  }

  /**
   * Scrolls an element based on its ID into the viewport.
   *
   * @param $element NodeElement
   *
   * @throws ElementNotFoundException
   */
  public function scrollToTextInElement($element) {
    if (null === $element) {
      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'id');
    }

    $id = $element->getAttribute('id');

    $this->getSession()
      ->executeScript('document.getElementById("' . addslashes($id) . '").scrollIntoView()');
  }

  /**
   * @Given The cursor is at the end of CKEditor
   * @Given The cursor is at the end of CKEditor :id
   */
  public function TheCursorIsAtTheEndOfCKEditor($id = NULL)
  {
    $id = $id ?: $this->getDefaultEditorId();

    $script = "
    var range = CKEDITOR.instances['$id'].createRange();
    range.moveToElementEditEnd( range.root );
    CKEDITOR.instances['$id'].getSelection().selectRanges( [ range ] );
    ";

    $this->getSession()->executeScript($script);
  }

}
