Feature: SiteFarm Editor Permissions
  To ensure that correct permissions are enabled
  As a Editor
  I should be restricted from some access and allowed for others

  Background:
    Given I am logged in as a user with the "Editor" role

  @api
  Scenario: Site Building pages that should be denied to Editor
    Given I am on "admin/structure/types"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/display-modes"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/views"
    Then I should get a "403" HTTP response

  @api
  Scenario: Page Building pages that should be accessible by Editor
    Given I am on "admin/structure/block"
    Then I should get a "200" HTTP response
    Given I am on "admin/structure/menu"
    Then I should get a "200" HTTP response
    Given I am on "admin/structure/taxonomy"
    Then I should get a "200" HTTP response

  @api
  Scenario: All Configuration pages should be denied to Editor
    Given I am on "admin/config/sitefarm"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/system/site-information"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/system/cron"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/performance"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/logging"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/maintenance"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/configuration"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/search/pages"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/search/path"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/sitefarm/clearcache"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/content/formats"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/crop"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/file-system"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/image-styles"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/image-toolkit"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/regional/date-time"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/services/rss-publishing"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/services/sharemessage"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/user-interface/shortcut"
    Then I should get a "403" HTTP response

  @api
  Scenario: Admin pages that should be denied to Editor
    Given I am on "admin/appearance"
    Then I should get a "403" HTTP response
    Given I am on "admin/modules"
    Then I should get a "403" HTTP response
    Given I am on "admin/people"
    Then I should get a "403" HTTP response
    Given I am on "admin/reports"
    Then I should get a "403" HTTP response

  @api
  Scenario: Admin pages that should be accessible by Editor
    Given I am on "admin/content"
    Then I should get a "200" HTTP response
    Given I am on "admin/help"
    Then I should get a "200" HTTP response
    Given I am on "node/add/sf_article"
    Then I should get a "200" HTTP response
    Given I am on "node/add/sf_page"
    Then I should get a "200" HTTP response
    Given I am on "node/add/sf_event"
    Then I should get a "200" HTTP response
    Given I am on "node/add/sf_person"
    Then I should get a "200" HTTP response
    Given I am on "node/add/sf_photo_gallery"
    Then I should get a "200" HTTP response

  @api
  Scenario: Unpublished nodes should be accessible by Editor
    Given "sf_page" content:
      | title       | status |
      | Test Page   | 0      |
      | Test Page 2 | 0      |
      And I am on "test-page"
    Then I should get a "200" HTTP response
    Given I am on "test-page-2"
    Then I should get a "200" HTTP response
