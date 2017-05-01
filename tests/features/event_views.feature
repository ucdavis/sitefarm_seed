Feature: A User should see lists of events
  In order to see events
  As an Anonymous visitor
  I want to be able to see lists of events on pages

  Background:
    Given I am an anonymous user
      And "sf_event_type" terms:
        | name           |
        | Views Category |
        | Views Cat 2    |
      And "sf_tags" terms:
        | name        |
        | Views Tag   |
        | Views Tag 2 |
      And "sf_event" content:
        | title        | field_sf_dates:value | field_sf_dates:end_value | field_sf_event_type |
        | First Event  | 2016-06-01T05:06:22  | 2016-06-01T06:06:22  | Views Category      |
        | Second Event | 2020-07-01T06:06:22  | 2020-07-01T07:06:22  | Views Cat 2         |
        | Third Event  | 2020-08-01T07:06:22  | 2020-08-01T08:06:22  | Views Category      |

  @api
  Scenario: Upcoming Events Block showing on the home page
    Given a block "views_block:sf_events_upcoming-block_1" is in the "sidebar_first" region
    When I am on the homepage
    Then I should not see "First Event" in the "Sidebar First Region"
      And I should see "Second Event" in the "Sidebar First Region"
      And I should see "Third Event" in the "Sidebar First Region"
      And I should see the ".vm-listing" element in the "Sidebar First Region"

  @api
  Scenario: Events Listing Page showing in the content region
    When I am on "/events"
    Then I should not see "First Event" in the "Content" region
      And I should see "Second Event" in the "Content" region
      And I should see "Third Event" in the "Content" region
      And I should see the ".vm-teaser" element in the "Content" region

  @api
  Scenario: Events Listing Block (duplicate of page) showing on the home page
    Given a block "views_block:sf_events_listing-block_1" is in the "sidebar_first" region
    When I am on the homepage
    Then I should not see "First Event" in the "Sidebar First Region"
      And I should see "Second Event" in the "Sidebar First Region"
      And I should see "Third Event" in the "Sidebar First Region"
      And I should see the ".vm-teaser" element in the "Sidebar First Region"

  @api
  Scenario: Event Type categories show in a block that can be filtered to show events in a selected category
    When I am on "/events"
      And I click "Views Category"
    Then I should see the ".category-filter" element in the "Sidebar Second Region"
      And I should see "Third Event" in the "Content" region
      And I should not see "Second Article" in the "Content" region
      And I should not see "First Event" in the "Content" region
    When I click "Views Cat 2" in the "Sidebar Second Region"
    Then I should see "Views Cat 2" in the ".category-filter__list-item--active" element
      And I should see "Second Event" in the "Content" region
      And I should not see "First Event" in the "Content" region
      And I should not see "Third Event" in the "Content" region

  @api
  Scenario: Event Exposed Filter should appear in the sidebar
    Given default nodes are unpublished
    When I am on "/events"
    Then I should see "Filter Results" in the "Sidebar First Region"
    When I fill in "title" with "Second"
      And I press "Apply Filter"
    Then I should see "Second Event" in the "Content" region
      And I should not see "First Event" in the "Content" region
      And I should not see "Third Event" in the "Content" region
    When I fill in "title" with ""
      And I fill in "start_date" with "August 1, 2020"
      And I press "Apply Filter"
    Then I should see "Third Event" in the "Content" region
      And I should not see "First Event" in the "Content" region
      And I should not see "Second Event" in the "Content" region
    When I fill in "title" with ""
      And I fill in "start_date" with "August 2, 2020"
      And I press "Apply Filter"
    Then I should see "There are currently no upcoming events to display." in the "Content" region
