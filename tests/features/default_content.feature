Feature: Default Content on Installation
  So that content is already populated when I install a site
  As an Administrator to the site
  I should see default content that is already in a new site

  Background:
    Given I am logged in as a user with the "administrator" role

  @api
  Scenario: Check that the Person Type Vocabulary is pre-populated
    Given I am at "admin/structure/taxonomy/manage/sf_person_type/overview"
      Then I should see "Faculty"
      And I should see "Researcher"
      And I should see "Staff"
      And I should see "Student"

  @api
  Scenario: Check that the Branding Vocabulary is pre-populated
    Given I am at "admin/structure/taxonomy/manage/sf_branding/overview"
    Then I should see "Unitrans Red"
      And I should see "Western Redbud"
      And I should see "California Poppy"
      And I should see "Golden Lupine"
      And I should see "Sunny Grass"
      And I should see "Blue Sky"
      And I should see "Rec Pool Blue"
      And I should see "Wine Grape"
      And I should see "MU Brick"
      And I should see "Hart Hall Stucco"
      And I should see "Sage Green"
      And I should see "Evergreen"
      And I should see "Winter Sky Gray"
      And I should see "Centennial Walk Gray"
      And I should see "Cork Oak"
      And I should see "South Hall Brown"

  @api
  Scenario: Check that the Article Categories Vocabulary is pre-populated
    Given I am at "admin/structure/taxonomy/manage/sf_article_category/overview"
    Then I should see "University News"
    And I should see "Education"
    And I should see "Environment"
    And I should see "Food & Agriculture"
    And I should see "Human & Animal Health"
    And I should see "Science & Technology"
    And I should see "Society, Arts & Culture"
    And I should see "Student Life"
