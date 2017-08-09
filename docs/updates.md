# Pushing Updates to Existing Sites

Once sites are in production on a Sub Profile built on SiteFarm Seed it is very
important to not break them when pushing updates.

> **Tip!** Although entire features can be reverted, it is often best only to revert
the explicit yml files which have been altered. This prevents unnecessary
changes that may break something an editor might have altered.

## 2 Parts to an Update

1. **New Sites:** Save config files in the `config/install/` directory of the sub
profile or sub profile modules. When a new site is installed it will
automatically install the config.

2. **Existing Sites:** Write an update hook to import new configuration or revert
existing site configuration to whatever the new default should be.

## "Importing" New Configuration Files

Importing is done when there is a new yaml config file. If the file has already
existed on the site then a "revert" is needed and not an import.

The [Configuration Update Manager](https://www.drupal.org/project/config_update)
is useful for importing and reverting configuration. It provides a service which
can both import and revert yaml config files.

This is probably the most standard form of importing new configuration from a
new yaml file:

```php
/** @var \Drupal\config_update\ConfigRevertInterface $config_update */
$config_update = \Drupal::service('config_update.config_update');
$config_update->import('system.simple', 'sitefarm_core.settings');
```

## "Reverting" Existing Config to the New Defaults

### Entire Files

Reverting an entire yaml file is similar to importing. Just use the `revert()`
method instead.

```php
/** @var \Drupal\config_update\ConfigRevertInterface $config_update */
$config_update = \Drupal::service('config_update.config_update');
$config_update->revert('system.simple', 'sitefarm_core.settings');
```

### Individual Configuration
Sometimes only small portions of a config need reverting. A perfect example
of this is user permissions. All user permissions are stored in a single user
role file `user.role.rolename`. Reverting the entire file could cause problems
with any changes that an administrator might have made on the site. So it would
be better in this case to only change the specific permissions intended.

To do this, load the current site configuration, add the permissions, and save
the config.

```php
// Set Permissions config for contributor users
$config = \Drupal::service('config.factory')->getEditable('user.role.contributor');
$permissions = $config->get('permissions');
$permissions[] = 'new permission setting';
$config->set('permissions', $permissions)->save();
```

> This doesn't have anything to do with yaml files, so be sure not to forget to
add the new config to the yaml files so that new sites will still get the new
config.


## Quick Reference of "Configuration Types"

This list is a quick reference of "Configuration Types" needed for importing or
reverting yml files. A complete list can be found by looking at the single
config update page and inspecting the dropdown list to find the “Configuration
Type” and “Configuration Name” `/admin/config/development/configuration/single/export`

* system.simple = Simple configuration
* action = Action
* base_field_override = Base field override
* block = Block
* contact_form = Contact form
* node_type = Content type
* crop_type = Crop type
* block_content_type = Custom block type
* date_format = Date format
* embed_button = Embed button
* entity_form_display = Entity form display
* entity_view_display = Entity view display
* features_bundle = Features bundle
* field_config = Field
* field_storage_config = Field storage
* entity_form_mode = Form mode
* image_style = Image style
* menu = Menu
* metatag_defaults = Metatag defaults
* pathauto_pattern = Pathauto pattern
* rdf_mapping = RDF mapping
* user_role = Role
* search_page = Search page
* sharemessage = Share Message
* shortcut_set = Shortcut set
* taxonomy_vocabulary = Taxonomy vocabulary
* editor = Text Editor
* filter_format = Text format
* tour = Tour
* view = View
* entity_view_mode = View mode
* webform = Webform
* webform_options = Webform options
* zone = Zone

## SiteFarm Seed Updates

When creating update hooks inside SiteFarm Seed that will edit individual config
it is important to wrap things inside conditionals. It can not be assumed that a
sub profile has not removed a specific line of config. So trying to revert
config that no longer exists will throw an error.

First check that the config exists:

```php
$config = \Drupal::service('config.factory')->getEditable('randomconfigitems');
$someconfig = $config->get('someconfig');
if ($someconfig) {
  $someconfig[] = 'new config setting';
  $config->set('someconfig', $someconfig)->save();
}
```