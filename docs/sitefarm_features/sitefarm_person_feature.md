# Sitefarm Person Feature

## Purpose
To provide configuration for a Person content type and related configuration. Add a person to profile an individual. 

## Content types Provided
* Person (sf_person)

## Taxonomy Vocabularies Included
* Person Type (sf_person_type)

## Views Provided
* Person(s) (sf_person_admin)
* Person Directory (sf_person_directory)
* Person Featured (sf_person_featured)
* Persons Content Contributions related back to Person (sf_persons_content_related_back_to_person)
* Persons Related to Content (sf_persons_related_to_content)

## Field Configuration provided
* Bio
* Hide from Directory
* Address
* Credentials
* Email
* Feature Content
* Documents
* First Name
* Last Name
* Meta Tags
* Name Prefix
* Office Hours
* Office Location
* Person Type
* Phone Number
* Position Title
* Portrait Image
* Tags
* Unit
* Website

## Base field configuration overrides
* Promoted to front page
* Display Name

## Other configuration included
* Pathauto pattern for Person content type

## Custom module code included
* Form alter to move fields into the additional options section on node editing screen. 
* Form alters to modify labels for "Add More" buttons.
* Form alter to hide the title and provide a default stub.
* Auto generate the title on Person nodes from the Prefix, First Name, Last Name and Credentials fields.
* 


