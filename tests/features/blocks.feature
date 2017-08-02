Feature: A User should be able to place a block
  In order for block to be placed
  As an Administrator
  I want to be able to place blocks.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "/admin/structure/block/library/bartik?region=content"

  @api
  Scenario: Block Config UI should not show Views Exposed contextual filters which are required
    When I click "Place block" in the "Content related by Tags" row
    Then I should see "Block description Content related by Tags"
      And I should not see "Content: ID"
      And I should not see "Exclude Current Content"
      And I should not see "Content: Has taxonomy term ID"
