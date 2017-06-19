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
