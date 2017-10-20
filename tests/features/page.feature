Feature: Basic Page Content Type
  Makes sure that the basic page content type was created during installation.

  Background:
    Given I am logged in as a user with the "administrator" role
    When I visit "node/add/sf_page"
      And I fill in the following:
        | Title | Testing title |

  @api
  Scenario: Make sure that the basic page provided by SiteFarm at installation is present.
    Then I should see "Basic page"

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present.
    Then CKEditor "edit-body-0-value" should exist

  @api
  Scenario: Ensure that the page Promote to Front page option is hidden.
    Then I should not see a "input[name='promote[value]']" element

  @api
  Scenario: Ensure that the page Create New Revision is checked.
    When I press "Save"
      And I click "Edit"
    Then the "revision" checkbox should be checked

  @api
  Scenario: Ensure that meta tag fields are present.
    Then I should see a "input[name='field_sf_meta_tags[0][basic][title]']" element
      And I should see a "textarea[name='field_sf_meta_tags[0][basic][description]']" element

  @api
  Scenario: A url alias should be auto generated for Basic Pages.
    When I press "Save"
    Then I should see "Testing title" in the "Page Title" region
      And I should be on "/testing-title"

  @api @javascript @local_files
  Scenario: A Primary image should be available to upload.
    When I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I wait for AJAX to finish
    Then I should not see a ".form-item-field-sf-primary-image-0-title" element
      And I should see "What's the plus sign for?"
    When I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I press "Save"
    Then I should see the success message "Basic Page Testing title has been created."

  @api @javascript
  Scenario: Basic pages should be able to go into the Main Menu
    Given the Administration Toolbar is hidden
    When I press "Menu settings"
      And I check the box "Provide a menu link"
      And I select "<Main navigation>" from "menu[menu_parent]"
      And I press "Save"
    Then I should see "Testing title" in the "Primary Menu Region"

  @api @javascript
  Scenario: Multiple file attachements to Basic Pages
    When I attach the file "test.pdf" to "files[field_sf_files_0][]"
      And I wait for AJAX to finish
      And I attach the file "test 2.pdf" to "files[field_sf_files_1][]"
      And I wait for AJAX to finish
      And I press "Save"
    Then I should see the link "test.pdf"
      And I should see the link "test 2.pdf"

  @api @javascript
  Scenario: Sub Pages should show up in a sidebar sub-nav menu
    Given the Administration Toolbar is hidden
      And a block "system_menu_block:main" is in the "sidebar_second" region
    When I press "Menu settings"
      And I check the box "Provide a menu link"
      And I select "<Main navigation>" from "menu[menu_parent]"
      And I press "Save"
      And I visit "node/add/sf_page"
      And the Administration Toolbar is hidden
      And I fill in the following:
        | Title | Sub Page |
      And I press "Menu settings"
      And I check the box "Provide a menu link"
      And I select "-- Testing title" from "menu[menu_parent]"
      And I press "Save"
    Then I should see the link "Sub Page" in the "Sidebar Second Region" region

  @api @javascript
  Scenario: Main menu should not show a menu weight field
    Given the Administration Toolbar is hidden
    When I press "Menu settings"
      And I check the box "Provide a menu link"
    Then I should see a ".form-item-menu-menu-parent" element
      And I should not see a ".form-item-menu-weight" element

  @api
  Scenario: Tags added to an Page
    When I fill in "field_sf_tags[target_id]" with "Tag Test, Tag Test 2"
      And I press "Save"
    Then I should see the link "Tag Test"
    When I click "Edit"
    Then the "field_sf_tags[target_id]" autocomplete field should contain "Tag Test, Tag Test 2"
