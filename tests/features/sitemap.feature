Feature: A User should see a sitemap
  In order to navigate the site as a whole
  As an Anonymous user
  I want to be able to see all available content.

  Background:
    Given I am an anonymous user

  @api
  Scenario: Menu links appear
    Given I visit "sitemap"
    Then I should see "Main navigation" in the "Content" region
      And I should see "Home" in the "Content" region
      And I should not see "Tags" in the "Content" region

  @api
  Scenario: Taxonomy links appear
    Given I am logged in as a user with the "administrator" role
      And "sf_article_type" terms:
        | name          |
        | Test Type |
      And "sf_article_category" terms:
        | name          |
        | Test Category |
      And I visit "node/add/sf_article"
      And I fill in the following:
        | Title                    | Testing title        |
        | field_sf_tags[target_id] | Tag Test, Tag Test 2 |
      And I select "Test Type" from "field_sf_article_type"
      And I select "Test Category" from "field_sf_article_category"
      And I press "Save"
      And I visit "sitemap"
    Then I should see "Article Categories" in the "Content" region
      And I should see "Test Category" in the "Content" region
      And I should see "Article Type" in the "Content" region
      And I should see "Test Type" in the "Content" region
      And I should see "Tag Test" in the "Content" region
      And I should see "Tag Test 2" in the "Content" region
