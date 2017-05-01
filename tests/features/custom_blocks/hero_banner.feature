Feature: A User should create a Hero Banner custom block
  In order for new Hero Banner block to be placed on a page
  As an Administrator
  I want to be able to create a Hero Banner.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "block/add/sf_hero_banner"

  @api @javascript @local_files
  Scenario: Add a Hero Banner to the content region
    When I fill in the following:
      | Title        | Title Text             |
      | Sub Title    | Sub Title Text         |
      | Video Button | https://youtu.be/12345 |
      And I fill in "field_sf_link[0][uri]" with "http://ucdavis.edu"
      And I fill in "field_sf_link[0][title]" with "Call to Action"
      And I attach the file "test_16x9.png" to "files[field_sf_image_0]"
      And I wait for AJAX to finish
      And I fill in "field_sf_image[0][alt]" with "alt text"
      And I press "Save"
    Then I should see the success message "Hero Banner HB: Title Text has been created"
    When I fill in "Title" with "Block Hero Banner"
      And I select "Content" from "Region"
      And I press "Save block"
    Then I should see the success message "The block configuration has been saved."
    When I am on the homepage
    Then I should see "Title Text" in the "Content"
      And I should see the ".hero-banner" element in the "Content" region
      And I should see an image in the "Content" region
      And I should see "Sub Title Text"
      And I should see the link "Play Video" in the "Content" region
      And I should see the link "Call to Action" in the "Content" region
    Given I delete the most recent custom block
    Then I should see the success message "The custom block HB: Title Text has been deleted."
