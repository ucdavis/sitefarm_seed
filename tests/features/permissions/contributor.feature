Feature: SiteFarm Contributor Permissions
  To ensure that correct permissions are enabled
  As a Contributor
  I should be restricted from some access and allowed for others

  Background:
    Given I am logged in as a user with the "Contributor" role

  @api
  Scenario: Contributors should be able to add content and view the content page
    Given I am on "admin/content"
    Then I should get a "200" HTTP response
    Given I am on "node/add"
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
  Scenario: Site Building pages that should be denied to Contributor
    Given I am on "admin/structure/block"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/display-modes"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/menu"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/taxonomy"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/types"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/views"
    Then I should get a "403" HTTP response

  @api
  Scenario: All Configuration pages should be denied to Contributor
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
  Scenario: All Admin pages should be denied to Contributor
    Given I am on "admin/appearance"
    Then I should get a "403" HTTP response
    Given I am on "admin/modules"
    Then I should get a "403" HTTP response
    Given I am on "admin/people"
    Then I should get a "403" HTTP response
    Given I am on "admin/reports"
    Then I should get a "403" HTTP response
    Given I am on "admin/help"
    Then I should get a "403" HTTP response
