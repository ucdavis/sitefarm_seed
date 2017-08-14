# Sitefarm Core Feature

## Purpose
To provide configuration for core components that get used by other features.

## Content types Provided
* None

## Taxonomy Vocabularies Included
* Tags

## Views Provided
* Content related by Tags
* SiteFarm Frontpage

## Field Configuration for taxonomy term provided
* Brand Color

## Base field storage configuration
* Block: body
* Block: icon choice
* Block: image
* Block: link
* Block: title
* Node: Hide from lists
* Node: Address
* Node: Credentials
* Node: Emails
* Node: Featured Status
* Node: Files
* Node: First Name
* Node: Last Name
* Node: Meta Tags
* Node: Name Prefix
* Node: Office Hours
* Node: Office Location
* Node: Phone Numbers
* Node: Position Title
* Node: Primary Image
* Node: Tags
* Node: Unit
* Node: Websites
* Taxonomy Term: Brand color
* Taxonomy Term: Primary Image

## Other configuration included
* Pathauto pattern for Taxonomy terms
* Share message module configuration for the Social Sharing buttons
* General sitemap settings and sitemap settings for Tags taxonomy term

## Custom module code included
* Preprocess to remove the pipe character from the output from the metatags module.
* Preprocess node to add indicator class to the output of a node a featured based on the featured field value.
* Form alter to move fields into the additional options section on node editing screen. 
* Form alters to modify labels for "Add More" buttons.
* Form alteration to add JavaScript that populates the path for the place block module.
* Alteration to modify the node edit form:
    * Remove the weight field
    * Change Meta Tags Label to SEO
    * Add title labels to sidebar
    * Add Javascript to ensure that required javascript fields don't go under the admin toolbar.
* Alteration of block forms for visibility
* Altering help text of the Body Summary field
* Primary image title text
* Focal point help text
* Fix the url of the Place Block link
* Provide a way to override image styles
* Flush caches on a theme install
