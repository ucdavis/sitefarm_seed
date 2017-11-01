Feature: Person Content Type
  Makes sure that the person content type was created during installation.

  Background:
    Given I am logged in as a user with the "administrator" role
    When I visit "node/add/sf_person"
      And I fill in the following:
        | Name Prefix | Dr   |
        | First Name  | John |
        | Last Name   | Doe  |
        | Credentials | Jr.  |

  @api
  Scenario: Make sure that the Person provided by SiteFarm at installation is present.
    Then I should see "Person"

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present for the Bio field.
    Then CKEditor "edit-body-0-value" should exist

  @api
  Scenario: Ensure that the person Promote to Front page option is hidden.
    Then I should not see a "input[name='promote[value]']" element

  @api
  Scenario: Ensure that the person Create New Revision is checked.
    When I press "Save"
      And I click "Edit"
    Then the "revision" checkbox should be checked

  @api
  Scenario: Ensure that meta tag fields are present.
    Then I should see a "input[name='field_sf_meta_tags[0][basic][title]']" element
      And I should see a "textarea[name='field_sf_meta_tags[0][basic][description]']" element

  @api
  Scenario: The Person title should be hidden and auto-generated using Prefix, First Name, Last Name, and Credentials
    Then I should not see "Display Name"
    When I press "Save"
    Then I should see "Dr John Doe Jr." in the "Page Title" region

  @api
  Scenario: A url alias should be auto generated for Persons.
    When I press "Save"
    Then I should see "Dr John Doe Jr." in the "Page Title" region
      And I should be on "/people/john-doe"

  @api @javascript @local_files
  Scenario: A Primary image should be available to upload.
    When I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I wait for AJAX to finish
      And I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I press "Save"
    Then I should see an image in the "Content" region
      And I should see the image alt "alt text" in the "Content" region

  @api
  Scenario: Persons should not able to go into the Main Menu
    Then I should not see the link "Menu settings"

  @api @javascript
  Scenario: Multiple file attachements to Person
    When I attach the file "test.pdf" to "files[field_sf_files_0][]"
      And I wait for AJAX to finish
      And I attach the file "test 2.pdf" to "files[field_sf_files_1][]"
      And I wait for AJAX to finish
      And I press "Save"
    Then I should see the link "test.pdf"
      And I should see the link "test 2.pdf"

  @api
  Scenario: Classify Persons with a single Person Type taxonomy
    Given "sf_person_type" terms:
      | name         |
      | Student Type |
    When I visit "node/add/sf_person"
      And I fill in the following:
        | First Name   | John |
        | Last Name    | Doe  |
      And I select "Student Type" from "field_sf_person_type"
      And I press "Save"
    Then I should not see the link "Student Type"
    When I click "Edit"
    Then the "field_sf_person_type" select should be set to "Student Type"

  @api @javascript
  Scenario: Address field should show US fields when selected
    Then the "field_sf_address[0][address][country_code]" select should be set to "- None -"
    When I select "United States" from "field_sf_address[0][address][country_code]"
      And I wait for AJAX to finish
    Then I should see a "input[name='field_sf_address[0][address][address_line1]']" element
      And I should see a "input[name='field_sf_address[0][address][locality]']" element
      And I should see a "select[name='field_sf_address[0][address][administrative_area]']" element
      And I should see a "input[name='field_sf_address[0][address][postal_code]']" element
    When I select "- None -" from "field_sf_address[0][address][country_code]"
      And I wait for AJAX to finish
      And I select "United States" from "field_sf_address[0][address][country_code]"
      And I wait for AJAX to finish
    Then I should see a "input[name='field_sf_address[0][address][address_line1]']" element

  @api
  Scenario: Person Directory shows group titles
    Given "sf_person_type" terms:
      | name         |
      | Student Type |
    When I visit "node/add/sf_person"
      And I fill in the following:
        | First Name   | John |
        | Last Name    | Doe  |
      And I select "Student Type" from "field_sf_person_type"
      And I press "Save"
    And I visit "people"
    Then I should see "Student Type" in the "Content" region

  @api
  Scenario: Add Person Button (link) is on the Person(s) admin view
    Given I am logged in as a user with the "site_manager" role
    When I visit "admin/content/person"
    Then I should see the link "Add Person"
