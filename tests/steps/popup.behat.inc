<?php

/**
 * @file
 * Contains \PopupSubContext.
 */

use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;
use Behat\Mink\Exception\ExpectationException;

/**
 * Dictionary to manage popups.
 *
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 * https://gist.github.com/blazarecki/2888851
 */
/**
 * Sub context for Javascript Popup Alert step definitions.
 */
class PopupSubContext extends DrupalSubContextBase implements DrupalSubContextInterface
{
  /**
   * @when I confirm the popup
   */
  public function confirmPopup()
  {
    $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
  }

  /**
   * @When I cancel the popup
   */
  public function cancelPopup()
  {
    $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
  }

  /**
   * @Then I should see :message in popup
   *
   * @param string $message The message.
   *
   * @return bool
   */
  public function assertPopupMessage($message)
  {
    return $message == $this->getSession()->getDriver()->getWebDriverSession()->getAlert_text();
  }

  /**
   * @Then I should not see an alert popup
   *
   * @return bool
   */
  public function assertNoPopupMessage()
  {
    $message = '';

    try {
      $message = $this->getSession()->getDriver()->getWebDriverSession()->getAlert_text();
    } catch (Exception $e) {
      return TRUE;
    }

    throw new ExpectationException("An unexpected Alert popup was found with the message: " . $message, $this->getSession()->getDriver());
  }

  /**
   * @When I fill :message in popup
   *
   * @param string $message The message.
   */
  public function setPopupText($message)
  {
    $this->getSession()->getDriver()->getWebDriverSession()->postAlert_text($message);
  }
}
