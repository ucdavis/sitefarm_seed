Feature: SiteFarm Site Manager Permissions
  To ensure that correct permissions are enabled
  As a Site Manager
  I should be restricted from some access and allowed for others

  Background:
    Given I am logged in as a user with the "Site Manager" role

  @api
  Scenario: Site Building pages that should be denied to Site Manager
    Given I am on "admin/structure/types"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/display-modes"
    Then I should get a "403" HTTP response
    Given I am on "admin/structure/views"
    Then I should get a "403" HTTP response
    Given I am on "admin/modules"
    Then I should get a "403" HTTP response

  @api
  Scenario: Page Building pages that should be accessible by Site Manager
    Given I am on "admin/structure/block"
    Then I should get a "200" HTTP response
    Given I am on "admin/structure/contact"
    Then I should get a "200" HTTP response
    Given I am on "admin/structure/menu"
    Then I should get a "200" HTTP response
    Given I am on "admin/structure/taxonomy"
    Then I should get a "200" HTTP response

  @api
  Scenario: Configuration pages that should be denied to Site Manager
    Given I am on "admin/config/content/formats"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/performance"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/logging"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/development/configuration"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/system/cron"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/user-interface/shortcut"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/crop"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/file-system"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/image-styles"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/media/image-toolkit"
    Then I should get a "403" HTTP response
    Given I am on "admin/config/services/sharemessage"
    Then I should get a "403" HTTP response

  @api
  Scenario: Configuration pages that should be accessible by Site Manager
    Given I am on "admin/config/sitefarm"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/people/accounts"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/content/honeypot"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/development/maintenance"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/search/metatag"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/search/redirect"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/search/redirect/404"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/sitefarm/clearcache"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/system/site-information"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/system/google-analytics"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/regional/settings"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/regional/date-time"
    Then I should get a "200" HTTP response
    Given I am on "admin/config/services/rss-publishing"
    Then I should get a "200" HTTP response

  @api
  Scenario: Admin pages that should be accessible by Site Manager
    Given I am on "admin/appearance"
    Then I should get a "200" HTTP response
    Given I am on "admin/content"
    Then I should get a "200" HTTP response
    Given I am on "admin/help"
    Then I should get a "200" HTTP response
    Given I am on "admin/people"
    Then I should get a "200" HTTP response
    Given I am on "admin/reports"
    Then I should get a "200" HTTP response

  @api
  Scenario: Administrators should be hidden from Site Managers on the People page
    Given users:
      | name       | mail           | roles         |
      | admin_user | admin@test.com | administrator |
      And I am on "admin/people"
    Then I should not see the link "admin_user"
