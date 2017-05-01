Feature: A User should not see any errors
  In order to make sure no pages have errors
  As an Anonymous visitor
  I want to make sure no pages show error messages

  @api
  Scenario: Default pages should be error free
    Given I am on the homepage
    Then I should not see an ".messages--error" element
    Given I am on "/news"
    Then I should not see an ".messages--error" element
    Given I am on "/blog"
    Then I should not see an ".messages--error" element
    Given I am on "/events"
    Then I should not see an ".messages--error" element
    Given I am on "/people"
    Then I should not see an ".messages--error" element
    Given I am on "/photo-galleries"
    Then I should not see an ".messages--error" element

  @api
  Scenario: Default pages should be error free
    Given "sf_page" content:
      | title           |
      | Test Page Error |
      And I am on "/test-page-error"
    Then I should not see an ".messages--error" element