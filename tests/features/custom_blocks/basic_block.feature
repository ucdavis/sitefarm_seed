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
      And I select "First Sidebar" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Basic Block" in the "Sidebar First Region"
      And I should see "This is Basic" in the "Sidebar First Region"
      And I should see the '.o-box' element in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Basic Block has been deleted."

  @api @javascript
  Scenario: Add a Basic Block with the Title Style Icon option selected
    Given the Administration Toolbar is hidden
    When I fill in the following:
      | Block description | Test Icon Title Block |
      And I press "Save"
    Then I should see the success message "Basic block Test Icon Title Block has been created"
    Given the Administration Toolbar is hidden
    When I fill in "Title" with "Title Icon Block"
      And I select "Icon" from "third_party_settings[block_style_plugins][title_style][title_style]"
      And I select "Mustang" from "third_party_settings[block_style_plugins][title_style][title_icon]"
      And I select "First Sidebar" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Title Icon Block" in the ".panel--icon.panel--icon-mustang" element
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Icon Title Block has been deleted."

  @api @javascript @local
  Scenario: Add a Basic Block with the Brand Textbox option selected
    Given the Administration Toolbar is hidden
      And default nodes are unpublished
    When I fill in the following:
      | Block description | Test Brand Block |
      And I put "This is Branded" into CKEditor
      And I press "Save"
    Then I should see the success message "Basic block Test Brand Block has been created"
    Given the Administration Toolbar is hidden
    When I fill in "Title" with "Brand Block"
      And I check the box "Color this box with a Branding Color"
      And I select "Unitrans Red" from "third_party_settings[block_style_plugins][brand_colors][branding_color]"
      And I select "First Sidebar" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Brand Block" in the ".brand-textbox" element
      And I should see "This is Branded" in the ".brand-textbox" element
      And I should see "This is Branded" in the ".category-brand--unitrans-red" element
      And I should not see the '.o-box' element in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Brand Block has been deleted."

  @api
  Scenario: Add a Basic Block with the Collapse option selected
    Given default nodes are unpublished
    When I fill in the following:
      | Block description | Test Collapse Block |
      | body              | This is Collapsed   |
      And I press "Save"
    Then I should see the success message "Basic block Test Collapse Block has been created"
    When I fill in "Title" with "Collapse Block"
      And I check the box "Collapse this block"
      And I select "First Sidebar" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Collapse Block" in the ".collapse__title" element
      And I should see "This is Collapsed" in the ".collapse__content" element
      And I should not see the '.o-box' element in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Collapse Block has been deleted."

  @api
  Scenario: Add a Basic Block with the Sibling Grid option selected
    Given default nodes are unpublished
    When I fill in the following:
      | Block description | Test Sibling Block |
      And I press "Save"
    Then I should see the success message "Basic block Test Sibling Block has been created"
    When I fill in "Title" with "Sibling Block"
    And I select "1/2 Column" from "third_party_settings[block_style_plugins][sibling_grid][sibling_grid]"
      And I select "First Sidebar" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Sibling Block" in the ".l-sibling-grid--half .panel__title" element
    Given I delete the most recent custom block
    Then I should see the success message "The custom block Test Sibling Block has been deleted."
