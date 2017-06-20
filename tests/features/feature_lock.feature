Feature: SiteFarm Feature Lock
  Check that SiteFarm Feature components have been locked to all but user 1

  Background:
    Given I am logged in as a user with the "Site Builder" role

  @api
  Scenario: SiteFarm Nodes Types should be locked
    Given I am at "admin/structure/types"
    Then I should see "Locked" in the "Admin Content" region
    Given I am on "admin/structure/types/manage/sf_page/fields"
    Then I should get a "403" HTTP response

  @api
  Scenario: SiteFarm Block Content Types should be locked
    Given I am at "admin/structure/block/block-content/types"
    Then I should see "Locked" in the "Admin Content" region
    Given I am on "admin/structure/block/block-content/manage/sf_basic"
    Then I should get a "403" HTTP response

  @api
  Scenario: SiteFarm Text Formats should be locked
    Given I am at "admin/config/content/formats"
    Then I should see "Locked" in the "Admin Content" region
    Given I am on "admin/config/content/formats/manage/sf_basic_html"
    Then I should get a "403" HTTP response

  @api
  Scenario: SiteFarm Taxonomy should be locked
    Given I am at "admin/structure/taxonomy"
    Then I should see "Add terms" in the "Admin Content" region
      And I should not see "Edit vocabulary" in the "Admin Content" region
      And I should not see "Manage fields" in the "Admin Content" region
      And I should not see "Manage form display" in the "Admin Content" region
    Given I am on "admin/structure/taxonomy/manage/sf_article_category"
    Then I should see "Access denied" in the "Page Title"

  @api
  Scenario: SiteFarm Pathauto Patterns should be locked
    Given I am at "admin/config/search/path/patterns"
    Then I should see "Locked" in the "Admin Content" region
    Given I am on "admin/config/search/path/patterns/sf_page"
    Then I should get a "403" HTTP response

#  @api
#  Scenario: SiteFarm Image Styles should be locked
#    Given I am at "admin/config/media/image-styles"
#    Then I should see "Flush" in the "Admin Content" region
#    Given I am on "admin/config/media/image-styles/manage/sf_thumbnail"
#    Then I should get a "403" HTTP response

  @api
  Scenario: SiteFarm Views should be locked
    Given I am at "admin/structure/views"
    Then I should see "Duplicate" in the "Admin Content" region
    Given I am on "admin/structure/views/view/sf_articles_recent"
    Then I should get a "403" HTTP response
