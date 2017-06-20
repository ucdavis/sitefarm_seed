Feature: A User should create a Focal Link custom block
  In order for new focal link blocks to be placed on a page
  As an Administrator
  I want to be able to create a Focal Link.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "block/add/sf_focal_link"

  @api
  Scenario: Add a Focal link
    When I fill in the following:
      | Title | Focal Link        |
      | Link  | http://google.com |
    Given the "Use Default Icons" checkbox should be checked
    When I press "Save"
    Then I should see the success message "Focal Link FL: Focal Link has been created"
    When I fill in "Title" with "Block Focal Link"
      And I select "Sidebar first" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see the link "http://google.com" in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block FL: Focal Link has been deleted."

  @api @javascript @local_files @local
  Scenario: Add a Focal link with an uploaded photo for the icon
    When I fill in the following:
      | Title | Focal Link 2        |
      | Link  | http://google.com 2 |
    Given the "Use Default Icons" checkbox should be checked
    And I should not see "Custom Image" in the ".field--name-field-sf-image" element
    And I uncheck the box "Use Default Icons"
    And I wait for AJAX to finish
    And I attach the file "test_16x9.png" to "files[field_sf_image_0]"
    And I wait for AJAX to finish
    And I fill in "field_sf_image[0][alt]" with "alt text"
    And I press "Save"
    Then I should see the success message "Focal Link FL: Focal Link 2 has been created"
    When I fill in "Title" with "Block Focal Link 2"
    And I select "Sidebar second" from "Region"
    And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see the link "http://google.com 2" in the "Sidebar Second Region"
    And I should not see the ".focal-link__icon" element in the "Sidebar Second Region"
    And I should see an image in the "Sidebar Second Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block FL: Focal Link 2 has been deleted."