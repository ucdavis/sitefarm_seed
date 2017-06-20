Feature: Event Content Type
  Makes sure that the event content type was created during installation.

  Background:
    Given I am logged in as a user with the "administrator" role
    When I visit "node/add/sf_event"
      And I fill in the following:
        | Title                               | Testing title |
        | field_sf_dates[0][value][date]      | 2016-06-01    |
        | field_sf_dates[0][value][time]      | 05:06:22      |
        | field_sf_dates[0][end_value][date]  | 2016-06-01    |
        | field_sf_dates[0][end_value][time]  | 06:06:22      |

  @api
  Scenario: Make sure that the Event content type provided by SiteFarm at installation is present.
    Then I should see "Event"

  @api
  Scenario: Events should not able to go into the Main Menu
    Then I should not see the link "Menu settings"

  @api
  Scenario: Ensure that the event Promote to Front page option is hidden.
    Then I should not see a "input[name='promote[value]']" element

  @api
  Scenario: Ensure that the event Create New Revision is checked.
    When I press "Save and publish"
      And I click "Edit"
    Then the "revision" checkbox should be checked

  @api
  Scenario: Ensure that meta tag fields are present.
    Then I should see a "input[name='field_sf_meta_tags[0][basic][title]']" element
    And I should see a "textarea[name='field_sf_meta_tags[0][basic][description]']" element

  @api
  Scenario: A url alias should be auto generated for Events.
    When I press "Save and publish"
    Then I should see "Testing title" in the "Page Title" region
    And I should be on "events/testing-title"

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present.
    Then CKEditor "edit-body-0-value" should exist

#  Don't use javascript here due to an issue with the time field acting weird
  @api @local_files
  Scenario: A Primary image should be available to upload.
    When I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I press "Save and publish"
#    Alt Text will be required so the form will rerender with the alt and title fields
    And I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I fill in "field_sf_primary_image[0][title]" with "title text"
      And I press "Save and publish"
    Then I should see an image in the "Content" region
      And I should see the image alt "alt text" in the "Content" region

  @api
  Scenario: Classify Events with a single Event Type taxonomy
    Given "sf_event_type" terms:
      | name                |
      | Event Test Category |
      | Second Event Term   |
    When I visit "node/add/sf_event"
      And I fill in the following:
        | Title                               | Testing title |
        | field_sf_dates[0][value][date]      | 2016-06-01    |
        | field_sf_dates[0][value][time]      | 05:06:22      |
        | field_sf_dates[0][end_value][date]  | 2016-06-01    |
        | field_sf_dates[0][end_value][time]  | 06:06:22      |
      And I select "Event Test Category" from "field_sf_event_type"
      And I press "Save and publish"
    Then I should see the link "Event Test Category" in the "Content" region
    When I click "Edit"
    Then the "field_sf_event_type" select should be set to "Event Test Category"

  @api
  Scenario: Tags added to an Event
    When I fill in "field_sf_tags[target_id]" with "Tag Test, Tag Test 2"
      And I press "Save and publish"
    Then I should see the link "Tag Test" in the "Content" region
      And I should see the link "Tag Test 2" in the "Content" region
    When I click "Edit"
    Then the "field_sf_tags[target_id]" autocomplete field should contain "Tag Test, Tag Test 2"

  @api
  Scenario: Multiple file attachements to Event
    When I attach the file "test.pdf" to "files[field_sf_files_0][]"
      And I press "Save and publish"
    Then I should see the link "test.pdf"
    When I click "Edit"
      And I attach the file "test 2.pdf" to "files[field_sf_files_1][]"
      And I press "Save and keep published"
    Then I should see the link "test 2.pdf"
      And I should see the link "test.pdf"

  @api
  Scenario: Locations on an Event
    When I fill in "field_sf_event_location[0][value]" with "My current Location"
      And I press "Save and publish"
    Then I should see "My current Location" in the "Content" region

  @api
  Scenario: Map Location Link on an Event
    When I fill in "field_sf_event_map_link[0][uri]" with "http://campusmap.ucdavis.edu/?b=107"
      And I fill in "field_sf_event_map_link[0][title]" with "Location for the event"
      And I press "Save and publish"
    Then I should see the link "Location for the event" in the "Content" region

  @api @javascript
  Scenario: Social share buttons on Event
    Given "sf_event" content:
      | title      |
      | Social Event |
    When I visit "events/social-event"
    Then I should see a ".at-icon-facebook" element
      And I should see a ".at-icon-twitter" element
      And I should see a ".at-icon-google_plusone_share" element
      And I should see a ".at-icon-email" element
      And I should see a ".at-icon-addthis" element


