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

  @api
  Scenario: Person Directory with Exposed Filter block
    Given a block "views_exposed_filter_block:sf_person_directory-page_1" is in the "sidebar_first" region
      And I am at "/people"
    Then I should see the ".node--view-mode-teaser" element in the "Content" region
      And I should see "First Name" in the "Sidebar First Region"
      And I should see "Last Name" in the "Sidebar First Region"
      And I should see "Position Title" in the "Sidebar First Region"
      And I should see "Unit" in the "Sidebar First Region"
    When I fill in "first" with "Davis"
      And I press "Apply Filter"
    Then I should see "Davis Dan" in the "Content" region
      And I should not see "John Test" in the "Content" region
      And I should not see "Jane Test" in the "Content" region
