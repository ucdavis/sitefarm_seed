# Adding New Configuration in a Sub Profile

## General

Extending Sitefarm Seed with new configuration in a sub profile is a matter of
exporting new yaml files and placing them in the `/config/install` directory.
These config files can also go into a module's `/config/install` directory in
the sub profile.

### Individual File Export

Sometimes it is more efficient to export a single file of configuration rather
than a bundle in a Feature.

To export a single yaml file of configuration, go to the Single Config Export
page. User 1 has a shortcut to this page in their toolbar.
 
![Screenshot of toolbar shortcut to Simple Config Export page](images/single-config-export-shortcut.png)

Choose the configuration type and name. Then copy the configuration and paste it
into a new yaml file named based on the "Filename" given at the bottom of the
page (under the textarea where the config can be copied).

### Features Module

Using the Features Module to bundle and export a group of configuration is best
for entirely new items like Content Types that will also have path aliases, new
image styles, multiple display modes, and associated views.

Documentation for using the Features module in Drupal 8 can be found here:
[https://www.drupal.org/docs/8/modules/features](https://www.drupal.org/docs/8/modules/features)

> **Tip:** The Features UI module is not enabled by default in Sitefarm Seed. So
it will need to be turned on first.

## New Content Type

## New Field on existing content type

## New View

## New Block instance

## Advanced additions with Config Actions
