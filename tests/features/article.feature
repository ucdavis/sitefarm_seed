Feature: A User should create an article
  In order for new articles to be published
  As an Administrator
  I want to be able to create Article content.

  Background:
    Given I am logged in as a user with the "administrator" role
    Given "sf_article_type" terms:
      | name          |
      | News          |
      | Test Category |
      | Second Term   |
      | Blog          |
    Then I visit "node/add/sf_article"
      And I fill in the following:
        | Title | Testing title |

  @api
  Scenario: Make sure that the article type provided by SiteFarm at installation is present.
    Then I should see "Article"

  @api @javascript
  Scenario: Ensure that the WYSIWYG editor is present.
    Then CKEditor "edit-body-0-value" should exist

  @api
  Scenario: Ensure that the article Promote to Front page option is hidden.
    Then I should not see a "input[name='promote[value]']" element

  @api
  Scenario: Ensure that the article Create New Revision is checked.
    Given I select "News" from "field_sf_article_type"
    When I press "Save"
    And I click "Edit"
    Then the "revision" checkbox should be checked

  @api
  Scenario: Ensure that meta tag fields are present on Articles.
    Then I should see a "input[name='field_sf_meta_tags[0][basic][title]']" element
    And I should see a "textarea[name='field_sf_meta_tags[0][basic][description]']" element

  @api
  Scenario: A url alias should be auto generated for Articles.
    Given I select "News" from "field_sf_article_type"
    When I press "Save"
    Then I should see "Testing title" in the "Content" region
      And I should be on "news/testing-title"

  @api @javascript @local_files
  Scenario: A Primary image should be available to upload.
    When I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
      And I wait for AJAX to finish
    Then I should see "Caption" in the ".form-item-field-sf-primary-image-0-title" element
      And I should see "What's the plus sign for?"
    When I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I fill in "field_sf_primary_image[0][title]" with "title text"
      And I press "Categorizing"
      And I select "News" from "field_sf_article_type"
      And I press "Save"
    Then I should see an image in the "Content" region
      And I should see the image alt "alt text" in the "Content" region
      And I should see the "img[title='title text']" element in the "Content" region

  @api
  Scenario: Tags added to an Article
    When I fill in "field_sf_tags[target_id]" with "Tag Test, Tag Test 2"
      And I select "News" from "field_sf_article_type"
      And I press "Save"
    Then I should see the link "Tag Test" in the "Content" region
      And I should see the link "Tag Test 2" in the "Content" region

  @api
  Scenario: Classify Articles with a single Category taxonomy
    Given "sf_article_category" terms:
      | name          |
      | Test Category |
      | Second Term   |
    When I visit "node/add/sf_article"
      And I fill in the following:
        | Title | Testing title |
      And I select "News" from "field_sf_article_type"
      And I select "Test Category" from "field_sf_article_category"
      And I press "Save"
    Then I should see the link "Test Category"

  @api
  Scenario: Classify Articles Type with a single Article Type taxonomy
    When I visit "node/add/sf_article"
      And I fill in the following:
        | Title | Testing title |
      And I select "Blog" from "field_sf_article_type"
      And I press "Save"
    When I click "Edit"
    Then the "field_sf_article_type" select should be set to "Blog"

  @api @javascript
  Scenario: Social share buttons on an Article
    Given I press "Categorizing"
      And I select "News" from "field_sf_article_type"
    When I press "Save"
    Then I should see a ".at-icon-facebook" element
      And I should see a ".at-icon-twitter" element
      And I should see a ".at-icon-google_plusone_share" element
      And I should see a ".at-icon-email" element
      And I should see a ".at-icon-addthis" element

  @api @javascript
  Scenario: Article teasers should strip html from the body summary
    Given the Administration Toolbar is hidden
    When I execute the "feature_block" command in CKEditor
      And I wait for AJAX to finish
      And I select the radio button "Align Right"
      And I press "OK"
      And I press "Categorizing"
      And I select "News" from "field_sf_article_type"
      And I press "Save"
    Then I should see "Title" in the "aside.wysiwyg-feature-block .wysiwyg-feature-block__title" element in the "Content" region
    When I visit "news/"
    Then I should not see the ".wysiwyg-feature-block__body" element in the "Content" region
