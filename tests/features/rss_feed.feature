Feature: A User should see a RSS Feed block
  In order to display a RSS Feed block
  As an Administrator
  I want to be able to add a RSS Feed block to a region.

  Background:
    Given I am logged in as a user with the "administrator" role
    And I visit "admin/structure/block/add/rss_feed_block/bartik?region=sidebar_first"

  @api
  Scenario: Add a RSS Feed block to the first sidebar region
    When I fill in the following:
      | RSS URL                           | https://fake.url |
      | Number of Posts                   | 3                |
      | Display feed text                 | paragraph        |
      | Paragraph character cutoff length | 500              |
      And I press "Save block"
    When I am on the homepage
    Then I should see "RSS Feed" in the "Sidebar First Region"
      And I should see the ".rss-feed__wrapper" element in the "Sidebar First Region"
      And I should see the "rss-feed" element with the "block-id" attribute set to "rssfeed" in the "Sidebar First Region"
      And I should see the "rss-feed" element with the ":count" attribute set to "3" in the "Sidebar First Region"
      And I should see the "rss-feed" element with the "text" attribute set to "paragraph" in the "Sidebar First Region"
      And I should see the "rss-feed" element with the ":cutoff" attribute set to "500" in the "Sidebar First Region"
      And I should see the "rss-feed" element with the ":more" attribute set to "1" in the "Sidebar First Region"
    When I visit "admin/structure/block/manage/rssfeed/delete"
      And I press "Remove"
    Then I should see "The block RSS Feed has been removed"

  @api @javascript
  Scenario: Render the RSS Feed block with Vue.js
    When I fill in the following:
      | RSS URL           | https://www.ucdavis.edu:443/articles/rss |
      | Number of Posts   | 3                |
      | Display feed text | snippet          |
      And I press "Save block"
    When I am on the homepage
      And I wait for AJAX to finish
    Then I should see "RSS Feed" in the "Sidebar First Region"
      And I should see an ".rss-feed__title" element
      And I should see a ".rss-feed__body" element
    When I visit "admin/structure/block/manage/rssfeed/delete"
      And I press "Remove"
    Then I should see "The block RSS Feed has been removed"
