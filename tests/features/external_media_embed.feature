Feature: A User should create an External Media Embed block
  In order for new External Media Embed block to be placed on a page
  As an Administrator
  I want to be able to create an External Media Embed block.

  Background:
    Given I am logged in as a user with the "administrator" role
      And I visit "admin/structure/block/add/external_media_embed_block/bartik?region=sidebar_first"

  @api @javascript
  Scenario: Add an External Media Embed block to the first sidebar region
      And I fill in "Embed URL" with "https://youtu.be/PAwB_t_iM7U"
      And the "Display title" checkbox should not be checked
      And I press "Save block"
    When I am on the homepage
    Then I should not see "External Embed" in the "Sidebar First Region"
      And I should see the '.responsive-embed' element in the "Sidebar First Region"
    When I visit "admin/structure/block/manage/externalmedia/delete"
      And I press "Remove"
    Then I should see "The block External Media has been removed"
