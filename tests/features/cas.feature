Feature: A programmatically created user should have a cas id added
  In order to simplify my login process
  As an New User
  I want to be able to log in with my CAS id.

  Background:
    Given users:
      | name    | mail                    | roles         |
      | Shawn   | sgdearmond@ucdavis.edu  | administrator |
      | Manager | wdtest@ucdavis.edu      | site_manager  |

  @api
  Scenario: CAS Username should appear in user/edit for cas users
    When I am logged in as "Shawn"
    And I visit "/user"
    And I click "Edit"
    Then the "cas_username" field should contain "triskal"

  @api
  Scenario: CAS Username should not appear in user/edit for non-cas users
    When I am logged in as "Manager"
    And I visit "/user"
    And I click "Edit"
    Then the "cas_username" field should contain ""

  @api
  Scenario: Check that the CAS login button is there and styled
    Given I am an anonymous user
    When I visit "/login"
    Then I should see the link "Log in"

  @api
  Scenario: Check that the link to the Drupal login is present
    Given I am an anonymous user
    When I visit "/login"
    Then I should see the link "Visit the administrative access page."

  @api
  Scenario: If logged in the CAS login page should offer a log out option
    When I am logged in as "Shawn"
    And I visit "/login"
    Then I should see the link "Log out"

  @api
  Scenario: The Simple CAS Settings form should be simple.
    Given I am logged in as a user with the site_manager role
    And I visit "/admin/config/sitefarm/cas"
    Then I should see a "#edit-forced-login" element
    And I should see a "#edit-user-accounts" element
    And I should see a "#edit-logout" element
    And I should not see a "#edit-user-accounts-restrict-password-management" element
    And I should not see a "#edit-logout-enable-single-logout" element
    And I should not see a "#edit-server" element
    And I should not see a "#edit-general" element
    And I should not see a "#edit-gateway" element
    And I should not see a "#edit-proxy" element
    And I should not see a "#edit-debugging" element
    And I should not see a "#edit-gateway" element

  @api
  Scenario: The Simple CAS Settings form should submit.
    Given I am logged in as a user with the site_manager role
    And I visit "/admin/config/sitefarm/cas"
    And for "Pages" I enter "/test"
    And I press the "Save configuration" button
    Then I should see the success message "The configuration options have been saved."

  @api
  Scenario: Values submitted by the Simple CAS Settings form should persist.
    Given I am logged in as a user with the administrator role
    And I visit "/admin/config/people"
    And I click "CAS"
    Then the "forced_login[paths][pages]" field should contain "/test"
