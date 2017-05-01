Feature: Layout and Block placement
  So that content is placed in its appropriate location
  As a visitor to the site
  I should see content arranged into logical regions of the site

  @api
  Scenario: Check that a block can be added to a region
    Given a block "system_powered_by_block" is in the "sidebar_first" region
    When I visit "/user"
    Then I should see the ".block-system-powered-by-block" element in the "Sidebar First Region"

  @api
  Scenario: Check that the correct layout class is added for a left hand sidebar
    Given a block "system_powered_by_block" is in the "sidebar_first" region
    When I visit "/user"
    Then I should see a ".l-davis" element

  @api @local
  Scenario: Check that the correct layout class is added for a right hand sidebar
    Given a block "system_powered_by_block" is in the "sidebar_second" region
    When I visit "/user"
    Then I should see a ".l-davis-flipped" element

  @api
  Scenario: Check that the correct layout class is when both sidebars have content
    Given a block "system_powered_by_block" is in the "sidebar_first" region
      And a block "system_powered_by_block" is in the "sidebar_second" region
    When I visit "/user"
    Then I should see a ".l-davis-3col" element

  @api
  Scenario: Quad Layout works by default in the Top Content regions
    Given the Sitefarm One theme setting "use_top_content_fixed" is set to "0"
      And a block "system_powered_by_block" is in the "top_content_first" region
      And I go to the homepage
    Then I should see a ".l-quad--full" element
    Given a block "system_powered_by_block" is in the "top_content_second" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--half" element
    Given a block "system_powered_by_block" is in the "top_content_third" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--third" element
    Given a block "system_powered_by_block" is in the "top_content_fourth" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad" element

  @api
  Scenario: Quad Layout works by default in the Bottom Content regions
    Given the Sitefarm One theme setting "use_bottom_content_fixed" is set to "0"
      And a block "system_powered_by_block" is in the "bottom_content_first" region
      And I go to the homepage
    Then I should see a ".l-quad--full" element
    Given a block "system_powered_by_block" is in the "bottom_content_second" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--half" element
    Given a block "system_powered_by_block" is in the "bottom_content_third" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--third" element
    Given a block "system_powered_by_block" is in the "bottom_content_fourth" region
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad" element

  @api
  Scenario: Quad Layout uses a "4 fixed" layout in the Top Content regions
    Given the Sitefarm One theme setting "use_top_content_fixed" is set to "1"
      And the Sitefarm One theme setting "top_content_quad_layout" is set to "l-quad--fixed"
      And a block "system_powered_by_block" is in the "top_content_first" region
      And I go to the homepage
    Then I should see a ".l-quad--fixed" element

  @api
  Scenario: Quad Layout uses a "4 fixed" layout in the Bottom Content regions
    Given the Sitefarm One theme setting "use_bottom_content_fixed" is set to "1"
    And the Sitefarm One theme setting "bottom_content_quad_layout" is set to "l-quad--fixed"
    And a block "system_powered_by_block" is in the "bottom_content_first" region
    And I go to the homepage
    Then I should see a ".l-quad--fixed" element

  @api
  Scenario: Fixed Quad Layout appears on only home page for Top Content regions
    Given the Sitefarm One theme setting "use_top_content_fixed" is set to "1"
      And the Sitefarm One theme setting "top_content_quad_front_only" is set to "1"
      And the Sitefarm One theme setting "top_content_quad_layout" is set to "l-quad--fixed"
      And a block "system_powered_by_block" is in the "top_content_first" region
      And I go to the homepage
    Then I should see a ".l-quad--fixed" element
    When I visit "/user"
    Then I should see a ".l-quad--full" element
    Given the Sitefarm One theme setting "top_content_quad_front_only" is set to "0"
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--fixed" element

  @api
  Scenario: Fixed Quad Layout appears on only home page for Bottom Content regions
    Given the Sitefarm One theme setting "use_bottom_content_fixed" is set to "1"
      And the Sitefarm One theme setting "bottom_content_quad_front_only" is set to "1"
      And the Sitefarm One theme setting "bottom_content_quad_layout" is set to "l-quad--fixed"
      And a block "system_powered_by_block" is in the "bottom_content_first" region
      And I go to the homepage
    Then I should see a ".l-quad--fixed" element
    When I visit "/user"
    Then I should see a ".l-quad--full" element
    Given the Sitefarm One theme setting "bottom_content_quad_front_only" is set to "0"
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-quad--fixed" element

  @api
  Scenario: Flex Footer can be applied to the Footer region
    Given the Sitefarm One theme setting "use_flex_footer" is set to "1"
      And a block "system_powered_by_block" is in the "footer" region
      And I go to the homepage
    Then I should see a ".flex-footer" element
    Given the Sitefarm One theme setting "use_flex_footer" is set to "0"
      And the cache has been cleared
      And I reload the page
    Then I should not see a ".flex-footer" element

  @api
  Scenario: The Split Sidebars should appear based on Appearance settings
    Given the Sitefarm One theme setting "left_sidebar_split" is set to "/user/*"
      And a block "system_powered_by_block" is in the "sidebar_first" region
      And a block "system_powered_by_block" is in the "sidebar_second" region
    When I visit "/user"
    Then I should see a ".l-davis" element
    Given the Sitefarm One theme setting "left_sidebar_split" is set to "/null"
      And the Sitefarm One theme setting "right_sidebar_split" is set to "/user/*"
      And the cache has been cleared
      And I reload the page
    Then I should see a ".l-davis-flipped" element
