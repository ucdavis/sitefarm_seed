Feature: CkEditor buttons and functionality
  Ensure that ckeditor is acting correctly and presenting content as intended

  Background:
    Given I am logged in as a user with the "administrator" role
    When I visit "node/add/sf_page"
      And I fill in the following:
        | Title | Testing title |
      And I wait for AJAX to finish

  @api @javascript
  Scenario: CKEditor's Basic HTML filter should have all needed buttons
    Then I should see a ".cke_button__bold" element
      And I should see a ".cke_button__italic" element
      And I should see a ".cke_button__strike" element
      And I should see a ".cke_button__removeformat" element
      And I should see a ".cke_button__link" element
      And I should see a ".cke_button__unlink" element
      And I should see a ".cke_button__anchor" element
      And I should see a ".cke_button__bulletedlist" element
      And I should see a ".cke_button__numberedlist" element
      And I should see a ".cke_button__justifyleft" element
      And I should see a ".cke_button__justifycenter" element
      And I should see a ".cke_button__justifyright" element
      And I should see a ".cke_button__undo" element
      And I should see a ".cke_button__redo" element
      And I should see a ".cke_button__source" element
      And I should see a ".cke_button__maximize" element
      And I should see a ".cke_combo__styles" element
      And I should see a ".cke_button__drupalimage" element
      And I should see a ".cke_button__url" element
      And I should see a ".cke_button__horizontalrule" element
      And I should see a ".cke_button__blockquote" element
      And I should see a ".cke_button__table" element
      And I should see a ".cke_button__specialchar" element
      And I should see a ".cke_button__media_link" element
      And I should see a ".cke_button__feature_block" element

  @api @javascript
  Scenario: The AutoSave plugin should save content after 10 seconds
    When I put "This is some body text" into CKEditor
      And I wait 11 seconds
      And I visit "node/add/sf_page"
      And I wait 2 seconds
    Then I should see "An auto-saved version" in popup
#    Now check that the alert does not come after saving
    When I cancel the popup
      And I fill in the following:
        | Title | Testing title |
      And I put "This is some body text" into CKEditor
      And I press "Save"
      And I click "Edit"
    Then I should not see an alert popup

  @api @javascript
  Scenario: Word and Character count should appear while typing
    Then I should see "Words: 0, Characters: 0" in the ".cke_wordcount" element
    When I put "This is some body text" into CKEditor
    Then I should see "Words: 5, Characters: 18" in the ".cke_wordcount" element

  @api @javascript
  Scenario: Heading Titles should have styles
    Given the Administration Toolbar is hidden
    When I click "Title" in the CKEditor style panel
      And I put "Title" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Title - Intro" in the CKEditor style panel
      And I put "Title - Intro" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Sub Title" in the CKEditor style panel
      And I put "Sub Title" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Sub Title - Auxiliary" in the CKEditor style panel
      And I put "Sub Title - Auxiliary" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Sub Title 2" in the CKEditor style panel
      And I put "Sub Title 2" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Sub Title 3" in the CKEditor style panel
      And I put "Sub Title 3" into CKEditor
    When I press "Save"
    Then I should see "Title" in the "h2" element in the "Content" region
      And I should see "Title - Intro" in the "h2.heading--underline" element in the "Content" region
      And I should see "Sub Title" in the "h3" element in the "Content" region
      And I should see "Sub Title - Auxiliary" in the "h3.heading--auxiliary" element in the "Content" region
      And I should see "Sub Title 2" in the "h4" element in the "Content" region
      # This needs to be Uppercase due to Bartik's style on an h5
      And I should see "SUB TITLE 3" in the "h5" element in the "Content" region

  @api @javascript
  Scenario: Block paragraph styles for CKEditor
    Given the Administration Toolbar is hidden
    When I click "Title" in the CKEditor style panel
      And I put "Normal" into CKEditor
      And I click "Normal" in the CKEditor style panel
      And I execute the "enter" command in CKEditor
    When I click "Clear Aligns" in the CKEditor style panel
      And I put "Clear Aligns" into CKEditor
    When I press "Save"
    Then I should see "Normal" in the "p" element in the "Content" region
      And I should see "Clear Aligns" in the "p.u-clear" element in the "Content" region

  @api @javascript
  Scenario: Alert message styles for CKEditor
    Given the Administration Toolbar is hidden
    When I click "Alert" in the CKEditor style panel
      And I put "Alert Base" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Alert - Success" in the CKEditor style panel
      And I put "Alert - Success" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Alert - Warning" in the CKEditor style panel
      And I put "Alert - Warning" into CKEditor
      And I execute the "enter" command in CKEditor
    When I click "Alert - Error" in the CKEditor style panel
      And I put "Alert - Error" into CKEditor
    When I press "Save"
    Then I should see "Alert Base" in the ".alert" element in the "Content" region
      And I should see "Alert - Success" in the ".alert.alert--success" element in the "Content" region
      And I should see "Alert - Warning" in the ".alert.alert--warning" element in the "Content" region
      And I should see "Alert - Error" in the ".alert.alert--error" element in the "Content" region

  @api @javascript
  Scenario: Pullquote styles for the Blockquote element in CKEditor
    Given the Administration Toolbar is hidden
    When I execute the "blockquote" command in CKEditor
      And I click "Pullquote" in the CKEditor style panel
      And I put "Pullquote Base" into CKEditor
      And I execute the "enter" command in CKEditor "2" times
    When I execute the "blockquote" command in CKEditor
      And I click "Pullquote - Left" in the CKEditor style panel
      And I put "Pullquote - Left" into CKEditor
      And I execute the "enter" command in CKEditor "2" times
    When I execute the "blockquote" command in CKEditor
      And I click "Pullquote - Right" in the CKEditor style panel
      And I put "Pullquote - Right" into CKEditor
    When I press "Save"
    Then I should see "Pullquote Base" in the ".pullquote" element in the "Content" region
      And I should see "Pullquote - Left" in the ".pullquote.u-align--left.u-width--half" element in the "Content" region
      And I should see "Pullquote - Right" in the ".pullquote.u-align--right.u-width--half" element in the "Content" region

  @api @javascript
  Scenario: Table styles for the Table element in CKEditor
    Given the Administration Toolbar is hidden
    When I execute the "table" command in CKEditor
      And I press "OK"
      And I click "Table Hover" in the CKEditor style panel
      And I put "Table Hover" into CKEditor
    Given The cursor is at the end of CKEditor
    When I execute the "table" command in CKEditor
      And I press "OK"
      And I click "Table Bordered" in the CKEditor style panel
      And I put "Table Bordered" into CKEditor
    Given The cursor is at the end of CKEditor
    When I execute the "table" command in CKEditor
      And I press "OK"
      And I click "Table Striped" in the CKEditor style panel
      And I put "Table Striped" into CKEditor
    Given The cursor is at the end of CKEditor
    When I execute the "table" command in CKEditor
      And I press "OK"
      And I click "Table Solid" in the CKEditor style panel
      And I put "Table Solid" into CKEditor
    When I press "Save"
    Then I should see "Table Hover" in the ".table--hover tbody tr td" element in the "Content" region
      And I should see "Table Bordered" in the ".table--bordered tbody tr td" element in the "Content" region
      And I should see "Table Striped" in the ".table--striped tbody tr td" element in the "Content" region
      And I should see "Table Solid" in the ".table--admin tbody tr td" element in the "Content" region

  @api @javascript
  Scenario: Unordered List styles for CKEditor
    Given the Administration Toolbar is hidden
    When I execute the "bulletedlist" command in CKEditor
      And I click "Flush List" in the CKEditor style panel
      And I put "Flush List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "Arrow List" in the CKEditor style panel
      And I put "Arrow List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "Arrow List - White" in the CKEditor style panel
      And I put "Arrow List - White" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "Bordered List" in the CKEditor style panel
      And I put "Bordered List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "FAQ List" in the CKEditor style panel
      And I put "FAQ List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "Pipe List" in the CKEditor style panel
      And I put "Pipe List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "Simple List" in the CKEditor style panel
      And I put "Simple List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "2 Columns" in the CKEditor style panel
      And I put "2 Columns" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "3 Columns" in the CKEditor style panel
      And I put "3 Columns" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "4 Columns" in the CKEditor style panel
      And I put "4 Columns" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "bulletedlist" command in CKEditor
      And I click "5 Columns" in the CKEditor style panel
      And I put "5 Columns" into CKEditor
    When I press "Save"
    Then I should see "Flush List" in the "ul.list--flush" element in the "Content" region
      And I should see "Arrow List" in the "ul.list--arrow" element in the "Content" region
      And I should see "Arrow List - White" in the "ul.list--white-arrow" element in the "Content" region
      And I should see "Bordered List" in the "ul.list--bordered" element in the "Content" region
      And I should see "FAQ List" in the "ul.list--faq" element in the "Content" region
      And I should see "Pipe List" in the "ul.list--pipe" element in the "Content" region
      And I should see "Simple List" in the "ul.list--simple" element in the "Content" region
      And I should see "2 Columns" in the "ul.l-column--2" element in the "Content" region
      And I should see "3 Columns" in the "ul.l-column--3" element in the "Content" region
      And I should see "4 Columns" in the "ul.l-column--4" element in the "Content" region
      And I should see "5 Columns" in the "ul.l-column--5" element in the "Content" region

  @api @javascript
  Scenario: Ordered List styles for CKEditor
    Given the Administration Toolbar is hidden
    When I execute the "numberedlist" command in CKEditor
      And I click "Multilevel List" in the CKEditor style panel
      And I put "Multilevel List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I execute the "numberedlist" command in CKEditor
      And I click "Outline List" in the CKEditor style panel
      And I put "Outline List" into CKEditor
      And I execute the "enter" command in CKEditor "3" times
    When I press "Save"
    Then I should see "Multilevel List" in the "ol.list--multilevel" element in the "Content" region
      And I should see "Outline List" in the "ol.list--outline" element in the "Content" region

  @api @javascript
  Scenario: Link and Button styles for CKEditor
    Given the Administration Toolbar is hidden
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button.edu"
      And I click "OK"
      And I click "Button" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button-large.edu"
      And I click "OK"
      And I click "Button - Large" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button-block.edu"
      And I click "OK"
      And I click "Button - Block" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button-alt.edu"
      And I click "OK"
      And I click "Button Alt" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button-alt-large.edu"
      And I click "OK"
      And I click "Button Alt - Large" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "button-alt-block.edu"
      And I click "OK"
      And I click "Button Alt - Block" in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I execute the "link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "URL" with "more.edu"
      And I click "OK"
      And I click "more..." in the CKEditor style panel
      And The cursor is at the end of CKEditor
      And I execute the "enter" command in CKEditor
    When I press "Save"
    Then I should see "http://button.edu" in the "a.btn--primary" element in the "Content" region
      And I should see "http://button-large.edu" in the "a.btn--lg.btn--primary" element in the "Content" region
      And I should see "http://button-block.edu" in the "a.btn--block.btn--primary" element in the "Content" region
      And I should see "http://button-alt.edu" in the "a.btn--alt" element in the "Content" region
      And I should see "http://button-alt-large.edu" in the "a.btn--alt.btn--lg" element in the "Content" region
      And I should see "http://button-alt-block.edu" in the "a.btn--alt.btn--block" element in the "Content" region
      And I should see "http://more.edu" in the "a.view-all" element in the "Content" region

  @api @javascript
  Scenario: Media Link button adds a widget and filters to wrap with a link
    Given the Administration Toolbar is hidden
    When I execute the "media_link" command in CKEditor
      And I wait for AJAX to finish
      And I fill in "Link URL" with "http://example.com"
      And I press "OK"
      And I press "Save"
    Then I should see "Title" in the "a.media-link .media-link__title" element in the "Content" region
      And I should see an image in the "Content" region
      And I should see "Content" in the ".media-link__content" element in the "Content" region

  @api @javascript
  Scenario: Feature Block button adds a widget to CKEditor
#    Right Aligned
    Given the Administration Toolbar is hidden
    When I execute the "feature_block" command in CKEditor
      And I wait for AJAX to finish
      And I select the radio button "Align Right"
      And I press "OK"
      And I press "Save"
    Then I should see "Title" in the "aside.wysiwyg-feature-block .wysiwyg-feature-block__title" element in the "Content" region
    And I should see "Content" in the ".wysiwyg-feature-block__body" element in the "Content" region
      And the element ".u-align--right" should exist
      And the element ".u-width--half" should exist
#    Left Aligned
    When I visit "node/add/sf_page"
      And I fill in the following:
        | Title | Testing title |
    Given the Administration Toolbar is hidden
    When I execute the "feature_block" command in CKEditor
      And I wait for AJAX to finish
      And I select the radio button "Align Left"
      And I press "OK"
      And I press "Save"
    Then I should see "Title" in the ".wysiwyg-feature-block__title" element in the "Content" region
      And the element ".u-align--left" should exist
      And the element ".u-width--half" should exist
#    No Alignment
    When I visit "node/add/sf_page"
      And I fill in the following:
        | Title | Testing title |
    Given the Administration Toolbar is hidden
    When I execute the "feature_block" command in CKEditor
      And I wait for AJAX to finish
      And I select the radio button "None"
      And I press "OK"
      And I press "Save"
    Then I should see "Title" in the ".wysiwyg-feature-block__title" element in the "Content" region
      And I should not see a ".u-align--left" element
      And I should not see a ".u-align--right" element
      And I should not see a ".u-align--half" element

  @api @javascript
  Scenario: URL Embed button renders a Youtube link as a responsive iframe
    Given the Administration Toolbar is hidden
    When I click the ".cke_button__url" element
      And I wait for AJAX to finish
      And I wait 2 seconds
      And I fill in "URL" with "https://youtu.be/PAwB_t_iM7U"
      And I click the ".ui-dialog-buttonset .js-form-submit" element
      And I wait for AJAX to finish
      And I wait 2 seconds
      And I press "Save"
    Then I should see the ".responsive-embed iframe" element in the "Content" region
