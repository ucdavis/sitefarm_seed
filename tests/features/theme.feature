Feature: Theme Settings
  Check that Theme settings in Sitefarm One theme correctly affect the page

  @api
  Scenario: Check that the WWW Quicklinks toggle shows and hides the menu
    Given I am logged in as a user with the "administrator" role
      And I am on the homepage
    Then I should see "Quick Links" in the "Navbar"
    Given I am at "admin/appearance/settings/sitefarm_one"
    When I uncheck the box "Use the Quick Links menu from www.ucdavis.edu"
      And I press "Save configuration"
      And I am on the homepage
    Then I should not see "Quick Links" in the "Navbar"
    Given I am at "admin/appearance/settings/sitefarm_one"
    When I check the box "Use the Quick Links menu from www.ucdavis.edu"
      And I press "Save configuration"
      And I am on the homepage
    Then I should see "Quick Links" in the "Navbar"



