Feature: A User should see lists of articles
  In order to see articles
  As an Anonymous visitor
  I want to be able to see lists of articles on pages

  Background:
    Given I am an anonymous user
      And "sf_article_type" terms:
        | name |
        | News |
        | Blog |
        | Views Type |
      And "sf_article_category" terms:
        | name           |
        | Views Category |
        | Views Cat 2    |
      And "sf_tags" terms:
        | name        |
        | Views Tag   |
        | Views Tag 2 |
      And "sf_article" content:
        | title          | field_sf_article_category | field_sf_tags | field_sf_article_type |
        | First Article  | Views Category            | Views Tag     | News                  |
        | Second Article | Views Cat 2               | Views Tag     | News                  |
        | Third Article  | Views Category            | Views Tag 2   | News                  |
        | Fourth Article | Views Category            | Views Tag 2   | Views Type            |
        | First Blog     | Views Category            | Views Tag     | Blog                  |
        | Second Blog    | Views Cat 2               | Views Tag     | Blog                  |
        | Third Blog     | Views Category            | Views Tag 2   | Blog                  |

  @api
  Scenario: Related Articles should appear on articles when having categories in common
    Given a block "views_block:sf_articles_related-block_1" is in the "sidebar_second" region
      And I am viewing a "sf_article" content:
        | title                     | Current Article |
        | field_sf_article_category | Views Category  |
        | field_sf_article_type     | News            |
    Then I should see "First Article" in the "Sidebar Second Region"
      And I should see "Third Article" in the "Sidebar Second Region"
      And I should not see "Second Article" in the "Sidebar Second Region"
      And I should not see "Current Article" in the "Sidebar Second Region"

  @api
  Scenario: Related Articles should appear on articles when having tags in common
    Given a block "views_block:sf_articles_related-block_1" is in the "sidebar_second" region
      And I am viewing a "sf_article" content:
        | title                 | Current Article |
        | field_sf_tags         | Views Tag       |
        | field_sf_article_type | News            |
    Then I should see "First Article" in the "Sidebar Second Region"
      And I should see "Second Article" in the "Sidebar Second Region"
      And I should not see "Third Article" in the "Sidebar Second Region"
      And I should not see "Current Article" in the "Sidebar Second Region"

  @api
  Scenario: Recent articles showing on the home page
    Given a block "views_block:sf_articles_recent-block_1" is in the "sidebar_first" region
    When I am on the homepage
    Then I should see "First Blog" in the "Sidebar First Region"
      And I should see "Second Article" in the "Sidebar First Region"
      And I should see "Third Article" in the "Sidebar First Region"
      And I should see the ".node--view-mode-listing" element in the "Sidebar First Region"

  @api
  Scenario: Articles are showing in the content region of the Articles page
    Given I am on "/articles"
    Then I should see "First Article" in the "Content" region
      And I should see "Second Article" in the "Content" region
      And I should see "Third Article" in the "Content" region
      And I should see the ".node--view-mode-teaser" element in the "Content" region

  @api
  Scenario: Latest blog posts showing in the content region of the Blog page
    Given I am on "/articles/blog"
    Then I should see "First Blog" in the "Content" region
      And I should see "Second Blog" in the "Content" region
      And I should see "Third Blog" in the "Content" region
      And I should see the ".node--view-mode-teaser" element in the "Content" region

  @api
  Scenario: Articles filtered by type and category that have spaces in their names are showing in the content region
    Given I am on "/articles/views-type/views-category"
    Then I should see "Fourth Article" in the "Content" region
    And I should see the ".node--view-mode-teaser" element in the "Content" region

  @api @javascript
  Scenario: Articles filtered by type and category that have spaces in their names are available as an RSS feed
    When I am on "/articles/views-type/views-category/feed.rss"
    Then I should see "Fourth Article"

  @api @javascript
  Scenario: Latest articles are available as an RSS feed
    When I am on "articles.rss"
    Then I should see "First Article"
      And I should see "Second Article"
      And I should see "Third Article"

  @api @javascript
# TODO: Re-enable this when Issue #196 is fixed
# Scenario: Latest blog articles are available as an RSS feed
#    When I am on "articles/blog/feed.rss"
#    Then I should see "First Blog"
#      And I should see "Second Blog"
#      And I should see "Third Blog"

  @api
  Scenario: Article Types show in a block that can filter to show articles in a selected category
    Given a block "views_block:sf_article_filter-block_1" is in the "sidebar_first" region
    When I am on "/articles"
    Then I should see "Filter by Article Type" in the "Sidebar First Region"
      And I should see "Blog" in the ".views-field-field-sf-article-type" element
      And I should see "First Article" in the "Content" region
      And I should see "Third Article" in the "Content" region
    When I click "Blog" in the "Sidebar First Region"
    Then I should see "First Blog" in the "Content" region
      And I should see "Second Blog" in the "Content" region
      And I should not see "First Article" in the "Content" region

  @api
  Scenario: Article Categories show in a block that can filter to show Blog posts in a selected category
    Given a block "views_block:sf_article_filter-block_2" is in the "sidebar_first" region
    When I am on "/articles/blog"
    Then I should see "Filter by Blog Category" in the "Sidebar First Region"
      And I should see "First Blog" in the "Content" region
      And I should see "Second Blog" in the "Content" region
      And I should see "Third Blog" in the "Content" region
    When I click "Views Cat 2" in the "Sidebar First Region"
      And I should see "Second Blog" in the "Content" region
      And I should not see "First Blog" in the "Content" region
      And I should not see "Third Blog" in the "Content" region

  @api
  Scenario: The title on the News page should be "Recent News Articles"
    Given I am an anonymous user
      And I visit "/articles/news"
    Then I should see the text "Recent News Articles" in the "Page Title" region

  @api
  Scenario: If an image is not uploaded no featured article block should show up on the News page
    Given I am logged in as a user with the "administrator" role
      And a block "views_block:sf_article_featured-sf_article_featured_block" is in the "content" region
      And I visit "node/add/sf_article"
      And I fill in the following:
        | Title | Testing Missing Featured View |
      And I select "News" from "field_sf_article_type"
      And I check the box "field_sf_featured_status[value]"
      And I press "Save"
      And I am an anonymous user
    When I visit "/articles/news"
    Then I should not see the ".block-views-blocksf-article-featured-sf-article-featured-block" element in the "Content" region

  @api @javascript
  Scenario: Single most recent featured article should appear to top of News page
    Given I am logged in as a user with the "administrator" role
      And a block "views_block:sf_article_featured-sf_article_featured_block" is in the "content" region
      And I visit "node/add/sf_article"
      And I fill in the following:
        | Title | Testing Featured View |
    When I press "Categorizing"
      And I select "News" from "field_sf_article_type"
    When I press "Promotion options"
      And I check the box "field_sf_featured_status[value]"
      And I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I wait for AJAX to finish
      And I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I fill in "field_sf_primary_image[0][title]" with "title text"
      And I press "Save"
      And I am an anonymous user
    When I visit "/news"
    Then I should see the ".block-views-blocksf-article-featured-sf-article-featured-block" element in the "Content" region

