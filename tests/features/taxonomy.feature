Feature: Taxonomy Terms
  Check that taxonomy terms behave correctly

  Background:
    Given I am logged in as a user with the "administrator" role
    When I visit "admin/structure/taxonomy/manage/sf_article_category/add"
      And I fill in the following:
        | Name | Testing Term |

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present.
    Then CKEditor "edit-description-0-value" should exist

  @api
  Scenario: A url alias should be auto generated for Terms.
    When I press "Save"
      And I visit "articles/testing-term"
    Then I should see "Testing Term" in the "Page Title" region
    # Clean up by deleting the term
    When I click "Edit"
      And I click "Delete"
      And I press "Delete"
    Then I should see "Deleted term Testing Term"

  @api @javascript @local_files
  Scenario: A Primary image should be available to upload.
    When I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I wait for AJAX to finish
      And I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I press "Save"
      And I visit "articles/testing-term"
    Then I should see an image in the "Page Title" region
      And I should see the image alt "alt text" in the "Page Title" region
    # Clean up by deleting the term
    When I click "Edit"
      And I click "Delete"
      And I press "Delete"
    Then I should see "Deleted term Testing Term"

  @api
  Scenario: Taxonomy Term page titles should change color based on the category color.
    When I select "Evergreen" from "field_sf_brand_color"
      And I press "Save"
      And I visit "articles/testing-term"
    Then I should see the ".category-brand--evergreen" element in the "Page Title"
    # Clean up by deleting the term
    When I click "Edit"
      And I click "Delete"
      And I press "Delete"
    Then I should see "Deleted term Testing Term"