Feature: A User should create a Basic Block custom block
  In order for new Basic Block blocks to be placed on a page
  As an Administrator
  I want to be able to create a Basic Block.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "block/add/sf_basic"

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present.
    Then CKEditor "edit-body-0-value" should exist

  @api
  Scenario: Add a Basic Block to the first sidebar region
    When I fill in the following:
      | Block description | Test Basic Block |
      | body              | This is Basic    |
      And I press "Save"
    Then I should see the success message "Basic block Test Basic Block has been created"
    When I fill in "Title" with "Basic Block"
      And I select "Sidebar first" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Basic Block" in the "Sidebar First Region"
      And I should see "This is Basic" in the "Sidebar First Region"
      And I should see the '.block' element in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Basic Block has been deleted."
