Feature: A User should see lists of content about a Person
  In order to see person content
  As an Anonymous visitor
  I want to be able to see lists of content about a Person

  Background:
    Given I am an anonymous user
    And "sf_person" content:
      | title     | field_sf_first_name | field_sf_last_name |
      | John Test | John                | Test               |
      | Jane Test | Jane                | Test               |
      | Davis Dan | Davis               | Dan                |
    And "sf_article" content:
        | title          | field_sf_person_reference |
        | First Article  | John Test                 |
        | Second Article | Davis Dan                 |
        | Third Article  | John Test                 |

  @api
  Scenario: Content related back to this person should appear in second sidebar as listings
    Given I am at "/people/john-test"
    Then I should see "First Article" in the "Sidebar Second Region"
      And I should see "Third Article" in the "Sidebar Second Region"
      And I should not see "Second Article" in the "Sidebar Second Region"
      And I should see the ".vm-listing" element in the "Sidebar Second Region"

  @api
  Scenario: Person Directory with Exposed Filter block
    Given I am at "/people"
    Then I should see the ".vm-teaser" element in the "Content" region
      And I should see "Filter Results" in the "Sidebar First Region"
      And I should see "First Name" in the "Sidebar First Region"
      And I should see "Last Name" in the "Sidebar First Region"
      And I should see "Position Title" in the "Sidebar First Region"
      And I should see "Unit" in the "Sidebar First Region"
    When I fill in "first" with "Davis"
      And I press "Apply Filter"
    Then I should see "Davis Dan" in the "Content" region
      And I should not see "John Test" in the "Content" region
      And I should not see "Jane Test" in the "Content" region
