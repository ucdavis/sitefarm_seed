@local_files
Feature: Photo Gallery Content Type
  Makes sure that the photo gallery content type was created during installation.

  Background:
    Given I am logged in as a user with the "administrator" role
      And "sf_photo_gallery_categories" terms:
        | name          |
        | Test Category |
        | Second Term   |
    When I visit "node/add/sf_photo_gallery"
      And I fill in the following:
        | Title | Gallery Name |
      And I attach the file "test_16x9.png" to "files[field_sf_gallery_photos_0][]"
      And I attach the file "test_16x9.png" to "files[field_sf_primary_image_0]"
    When I press "Save and publish"
      And I fill in "field_sf_gallery_photos[0][alt]" with "alt text"
      And I fill in "field_sf_primary_image[0][alt]" with "alt text"

  @api
  Scenario: Make sure that the photo gallery type provided by SiteFarm at installation is present.
    Then I should see "Photo Gallery"

  @api @javascript
  Scenario: Social share buttons on Photo Gallery page
    Given "sf_photo_gallery" content:
      | title                |
      | Social Photo Gallery |
    When I visit "photo-galleries/social-photo-gallery"
    Then I should see a ".at-icon-facebook" element
      And I should see a ".at-icon-twitter" element
      And I should see a ".at-icon-google_plusone_share" element
      And I should see a ".at-icon-email" element
      And I should see a ".at-icon-addthis" element

  @api @javascript
  Scenario: Title attribute field on photo field should be labeled as Caption instead
    Given I wait for AJAX to finish
    Then I should see "Caption" in the ".form-item-field-sf-gallery-photos-0-title" element
    Then I should see "Caption" in the ".form-item-field-sf-primary-image-0-title" element

  @api
  Scenario: Ensure that the event Promote to Front page option is hidden.
    Then I should not see a "input[name='promote[value]']" element

  @api
  Scenario: Ensure that the photo_gallery Create New Revision is NOT checked.
    When I press "Save and publish"
      And I click "Edit"
    Then the "revision" checkbox should not be checked

  @api @javascript
  Scenario: Ensure that the custom help text is shown.
    Given I wait for AJAX to finish
    Then I should see "What's the plus sign for?"

  @api
  Scenario: Classify Galleries with a single Category taxonomy.
    When I select "Test Category" from "field_sf_gallery_category"
      And I press "Save and publish"
    Then I should see the link "Test Category"
    Given a block "views_block:sf_photo_gallery_category_filter-block_1" is in the "sidebar_second" region
    When I visit "photo-galleries"
    Then I should see "Filter by Category" in the "Sidebar Second Region"
      And I should see the link "Test Category" in the "Sidebar Second Region"
      And I should see the link "Second Term" in the "Sidebar Second Region"

  @api @javascript
  Scenario: Photo Galleries poster displays
    Given I wait for AJAX to finish
    When I fill in "field_sf_gallery_photos[0][alt]" with "alt text"
      And I fill in "field_sf_primary_image[0][alt]" with "alt text"
      And I press "Save and publish"
    When I visit "photo-galleries"
    Then I should see "Gallery Name" in the ".node--view-mode-poster" element

  @api
  Scenario: Slideshow Photo Gallery block added to a region
    When I press "Save and publish"
      And I visit "admin/structure/block/add/slideshow_gallery_block/bartik?region=sidebar_first"
      And I reference "node" "Gallery Name" in "Gallery Title"
      And the "settings[display][show_title]" checkbox should be checked
      And the "settings[display][lazy_load]" checkbox should be checked
      And the "settings[display][slider_nav]" checkbox should not be checked
      And I press "Save block"
    When I am on the homepage
    Then I should see "Slideshow Photo Gallery" in the "Sidebar First Region"
      And the element ".slideshow" should exist
      And I should see "Gallery Name" in the "Sidebar First Region"
    # Edit
    When I visit "admin/structure/block/manage/slideshowphotogallery"
      And I uncheck the box "settings[label_display]"
      And I uncheck the box "settings[display][show_title]"
      And I uncheck the box "settings[display][lazy_load]"
      And I check the box "settings[display][slider_nav]"
      And I press "Save block"
    When I am on the homepage
    Then I should not see "Slideshow Photo Gallery" in the "Sidebar First Region"
      And I should not see "Gallery Name" in the "Sidebar First Region"
      And the element ".slideshow" should exist
      And the element ".slider-nav" should exist
    # Delete the block
    When I visit "admin/structure/block/manage/slideshowphotogallery/delete"
      And I press "Remove"
    Then I should see "The block Slideshow Photo Gallery has been removed"
