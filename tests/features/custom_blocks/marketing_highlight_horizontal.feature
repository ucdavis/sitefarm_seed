Feature: A User should create a Marketing Highlight - Horizontal  custom block
  In order for new Marketing Highlight - Horizontal blocks to be placed on a page
  As an Administrator
  I want to be able to create a Marketing Highlight - Horizontal.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "block/add/sf_marketing_highlight_horizntl"
      And I fill in the following:
        | Title             | Marketing Highlight - Horizontal |
        | Link              | http://google.com                |

  @api @javascript @local_files
  Scenario: Add a standard Marketing Highlight - Horizontal custom block
    When I attach the file "test_16x9.png" to "files[field_sf_image_0]"
      And I wait for AJAX to finish
      And I fill in "field_sf_image[0][alt]" with "alt text"
      And I press "Save"
    Then I should see the success message "Marketing Highlight - Horizontal MHH: Marketing Highlight - Horizontal has been created"
    Given the Administration Toolbar is hidden
    When I fill in "Title" with "Block Marketing Highlight - Horizontal"
      And I select "Sidebar first" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see the link "http://google.com" in the "Sidebar First Region"
      And I should see the "#block-mhhmarketinghighlighthorizontal" element in the "Sidebar First Region"
    Given I delete the most recent custom block
    Then I should see the success message "The custom block MHH: Marketing Highlight - Horizontal has been deleted."
