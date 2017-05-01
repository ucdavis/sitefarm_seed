Feature: A User should see an aggiefeed block
  In order to display an aggiefeed block
  As an Administrator
  I want to be able to add an aggiefeed block to a region.

  Background:
    Given I am logged in as a user with the "administrator" role

  @api
  Scenario: An AggieFeed block can be placed on the homepage
    Given a block "aggiefeed_block" is in the "sidebar_first" region
    When I am on the homepage
    And I should see the ".aggiefeed-element" element in the "Sidebar First Region"
