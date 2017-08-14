# Custom Modules

## CkEditor Autosave
Implements the CKEditor Autosave plugin to save the textarea to HTML5 local storage. 

## CkEditor Feature Box
Implements the CKEditor Feature Box widget plugin which outputs a predefined set of markup that is an aside tag with classes that can be targeted. In the textarea this provides a basic layout for the editor to populate the box with the correct data. The markup of this box is locked to insure against unwanted output.

## CkEditor Teaser (Media) Link Box
Implements the CKEditor Teaser Link Box widget plugin which outputs a predefined set of markup that is an div tag with media-link classes that can be targeted. The output looks a lot like what you would have with a normal teaser or media link with a thumbnail floated to the left and title and summary content on the right In the textarea this provides a basic layout for the editor to populate the box with the correct data. The markup of this box is locked to insure against unwanted output.

## External Media Embed
Provides a custom block that allows the user to Embed a video or other third party media in a block via an embed URL. For example, from YouTube, a simple url can be entered (https://youtu.be/PAwB_t_iM7U) and the video will be displayed.

## Lock SiteFarm Features
Locks down SiteFarm features so that they cannot be altered. This insures that updates to the features we provide can be performed without destroying domain specific configurations. The node types, block types, filter formats, pathauto patterns, taxonomy vocabularies, views, and image_styles that are locked by this module are configurable. 
### Here is what gets locked:
* Lock down Image styles except for the Flush operation.
* Partially lock Taxonomy, but still leave listing and adding terms.
* Partially lock Views, but still leave duplicating and disabling.
### There are also several administration pages that are only accessible to those with the Administrator role for any given entity in the configuration:
#### Nodes
* Form display
* View Display
* Field Delete
* Node storage editing
* Field editing
* Node Type editing and deleting
#### Block Content Types
* Form display
* View Display
* Field Delete
* Block storage editing
* Block Type editing and deleting
#### Text Filters
* Disable filter format
* Edit filter format
* Auto label filter format
#### Image Styles
* Delete image style
* Edit image style
* Add, edit and delete effects
#### Taxonomy
* Form Display
* View display
* Field editing and deleting
* Field storage editing
* Vocabulary editing and deleting
#### Pathauto Patterns
* Delete pattern
* Disable pattern
* Edit pattern
* Enable pattern
#### Views
* Delete view
* Edit display
* Edit view

## RSS Feed Block
Add a custom block which displays an RSS feed. The configuration for the block includes:
* Add an RSS feed URL
* How many Items get displayed
* How much of the feed appears
* Text cutoff for paragraphs

## Site Credits
Display a block of site credits info. The configuration for the information included is available on the Site Settings configuration page (/admin/config/system/site-information). 

## Sitefarm Custom Social Links
Provides custom icon set for the social_media_links module.

## Simple Configuration
Simplifies the configuration page so it is more usable to Site Managers.
### Hides the following configuration sections:
* Cron Settings
* Performance_settings
* Logging settings
* File system settings
* Image toolkit settings
* System status
* Database log overview
* Photoswipe Settings
* Simple sitemap settings
* Acquia Connector settings

## Sitefarm Summary
Provides a text formatter option for "Summary Only"