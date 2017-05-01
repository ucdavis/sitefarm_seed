Feature: An Administrator should see lists of persons in the persons content page

  Background:
    Given I am logged in as a user with the "administrator" role
    When "sf_person" content:
      | title     | field_sf_first_name | field_sf_last_name |
      | Davis Dan | Davis               | Dan                |
      | Davis Dan2 | Davis               | Dan2                |
      | Davis Dan3 | Davis               | Dan3                |
      | Davis Dan4 | Davis               | Dan4                |
      | Davis Dan5 | Davis               | Dan5                |
      And I visit "/admin/content/person"
  @api
  Scenario: I should see data in the administration person list
    Then I should see "Davis Dan" in the "Admin Content" region
      And I should see "Davis Dan2" in the "Admin Content" region
      And I should see "Davis Dan3" in the "Admin Content" region
      And I should see "Davis Dan4" in the "Admin Content" region
      And I should see "Davis Dan5" in the "Admin Content" region