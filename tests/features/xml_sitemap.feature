Feature: As a Search Engine Bot I should see an XML sitemap
  For search engines to easily navigate the site as a whole
  As an Administrator user
  I want to be able to see that XML sitemap configuration is correct.

  Background:
    Given I am logged in as a user with the "administrator" role

  @api
  Scenario: XML Sitemap Entity support is enabled
    Given I visit "admin/config/search/simplesitemap/entities"
    Then the "Enable content (node) support" checkbox should be checked
      And the "Enable custom menu link (menu_link_content) support" checkbox should be checked
      And the "Enable taxonomy term (taxonomy_term) support" checkbox should be checked

  @api
  Scenario: XML Sitemap Nodes should be enabled
    When I visit "node/add/sf_article"
    Then the "Index this sf_article entity (default)" checkbox should be checked
    When I visit "node/add/sf_event"
    Then the "Index this sf_event entity (default)" checkbox should be checked
    When I visit "node/add/sf_page"
    Then the "Index this sf_page entity (default)" checkbox should be checked
    When I visit "node/add/sf_person"
    Then the "Index this sf_person entity (default)" checkbox should be checked
    When I visit "node/add/sf_photo_gallery"
    Then the "Index this sf_photo_gallery entity (default)" checkbox should be checked

  @api
  Scenario: XML Sitemap Taxonomy should be enabled
    When I visit "admin/structure/taxonomy/manage/sf_article_category/add"
    Then the "Index this sf_article_category entity (default)" checkbox should be checked
    When I visit "admin/structure/taxonomy/manage/sf_article_type/add"
    Then the "Index this sf_article_type entity (default)" checkbox should be checked
    When I visit "admin/structure/taxonomy/manage/sf_event_type/add"
    Then the "Index this sf_event_type entity (default)" checkbox should be checked
    When I visit "admin/structure/taxonomy/manage/sf_person_type/add"
    Then the "Index this sf_person_type entity (default)" checkbox should be checked
    When I visit "admin/structure/taxonomy/manage/sf_photo_gallery_categories/add"
    Then the "Index this sf_photo_gallery_categories entity (default)" checkbox should be checked
    When I visit "admin/structure/taxonomy/manage/sf_tags/add"
    Then the "Index this sf_tags entity (default)" checkbox should be checked

  @api
  Scenario: XML Sitemap custom links are enabled
    Given I visit "admin/config/search/simplesitemap/custom"
    Then the "Relative Drupal paths" field should contain """
/ 1.0
/blog 0.9
/news 0.9
/events 0.9
/people 0.7
/photo-galleries 0.7
    """

