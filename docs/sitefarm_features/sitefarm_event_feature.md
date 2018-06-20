# Sitefarm Event Feature

## Purpose
To provide Event content type and related configuration. Create an event which will be tied to a calendar date. 

## Content types Provided
* Event (sf_event)

## Taxonomy Vocabularies Included
* Event Category (sf_event_type)

## Views Provided
* Events Category (sf_events_category_filter)
* Filter Events Listing (sf_events_listing)
* Events Upcoming (sf_events_upcoming)

## Field Configuration provided
* Body
* Event Date
* Article Type 
* Location
* Link to a Map
* Event Category
* Feature Content
* Documents
* Meta Tags
* Primary Image
* Tags
* Brand Color

## Base field configuration overrides
* Promoted to front page

## Other configuration included
* Pathauto pattern for Event content type

## Custom module code included
* Plugin implementation of the 'Merge' formatter for 'daterange' fields: This formatter renders the data range using `<time>` elements, with configurable options for showing the day name and time.
* Form alters for moving some fields to the advanced tabs. 
* Form modification for the date field to make format more usable. 
* Form modification of the validation of form to handle to better date formatting. 
