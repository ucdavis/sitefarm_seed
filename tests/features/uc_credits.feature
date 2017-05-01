Feature: UC Credits
  Check that UC Credits are added into the footer and are configurable

  @api
  Scenario: Check that the UC Credits block is in the footer
    When I am on the homepage
    Then I should see "Copyright" in the "Footer Credits Region"

  @api
  Scenario: Add contact and privacy links
    Given I am logged in as a user with the "administrator" role
      And "sf_page" content:
        | title          |
        | Test Page      |
        | Test Page 2    |
    When I am on "/admin/config/system/site-information"
      And I fill in "uc_credits_contact" with "/test-page"
      And I fill in "uc_credits_privacy" with "/test-page-2"
      And I press "Save configuration"
      And I am on the homepage
      And I click "Questions or comments?" in the "Footer Credits Region"
    Then the url should match "/test-page"
    And I click "Privacy & Accessibility" in the "Footer Credits Region"
    Then the url should match "/test-page-2"

  @api
  Scenario: Internal links should automatically get prefixed with a /
    Given I am logged in as a user with the "administrator" role
    And "sf_page" content:
      | title          |
      | Test Page      |
      | Test Page 2    |
    When I am on "/admin/config/system/site-information"
      And I fill in "uc_credits_contact" with "test-page"
      And I press "Save configuration"
    Then I should see the success message "The configuration options have been saved"
      And the "uc_credits_contact" field should contain "/test-page"

  @api
  Scenario: The contact field should also accept an email address
    Given I am logged in as a user with the "administrator" role
    When I am on "/admin/config/system/site-information"
      And I fill in "uc_credits_contact" with "test@test.com"
      And I fill in "uc_credits_privacy" with "http://google.com"
      And I press "Save configuration"
    Then I should see the success message "The configuration options have been saved"
      And the "uc_credits_contact" field should contain "test@test.com"

  @api
  Scenario: Invalid paths should throw an error
    Given I am logged in as a user with the "administrator" role
    When I am on "/admin/config/system/site-information"
      And I fill in "uc_credits_contact" with "invalid-internal-path"
      And I press "Save configuration"
    Then I should see the error message "Please provide a valid URL or email address for the Site Contact."
    When I fill in "uc_credits_contact" with "http://valid-external-site.com"
      And I fill in "uc_credits_privacy" with "invalid-external-site.com"
      And I press "Save configuration"
    Then I should see the error message "Please provide a valid URL."
