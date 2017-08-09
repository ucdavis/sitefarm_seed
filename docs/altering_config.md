# Altering Configuration from SiteFarm Seed in a Sub Profile

Often the default node configuration of Sitefarm Seed features such as the Article
content type will need to be overridden to meet the needs of a sub profile.

## General
In its simplest, altering configuration from SiteFarm Seed means copying config
files out of SiteFarm Seed and placing them into a sub profile's
`/config/install` directory. 

> **Warning!** Configuration files may be placed into a module's
`/config/install` directory only if it is created by Features and Features is
enabled.

By doing this, when a new site is installed it will first look to the sub
profile for config instead of SiteFarm Seed.

### Individual File Export

For most changes, make whatever changes need to take place via the UI and then
export the new config via the Simple Config Export page `/admin/config/development/configuration/single/export`

> **Important!** Remember to strip out any UUID or defualt_config_hash information

```yaml
uuid: e1083155-1afb-4d23-acff-17729aec7bc3
_core:
  default_config_hash: p_AtPbTd6niywC8P6zRfczYFjPDsejfH7Qcwo47ixCM
```

Now just paste this config into a the same named file in the sub profile.

### Features Module
Using the Features Module for doing overwrites is a little more complicated. The
reason for this is that any updates that the Features module does will be either
placed into SiteFarm Seed or downloaded in whole. Downloading is the best option
but it makes it difficult to know which files with changes actually need to be
added to the sub profile. Although the entire feature module could be added, 
this would basically be an entire rewrite of the SiteFarm Seed module which
means that future updates would be lost.

So it is best to learn what files get affected when changes are made.

## Altering Content Types

To alter a content type, copy the `node.type.machine_name.yml` file into a sub
profile.

Occasionally there will be other files that get changed instead such as
`core.base_field_override.node.machine_name.promote.yml`. This is the config
file generated when the checkbox to "promote to frontpage" is disabled.

## Altering Fields on existing content types

When altering a single field instance, copy the
`field.field.node.content_type_machine_name.field_machine_name.yml` file into
the subprofile. It is not recommended to override any base fields due to
unforeseen side effects on any other field depending on it.

## Altering Views

Altering a view requires copying the single view config file
`views.view.machine_name.yml` into the subprofile.

## Advanced alterations with Config Actions

[Config Actions](https://www.drupal.org/project/config_actions) is a module that
can be included in a sub profile which provides much more fine-grained control
over altering configuration. This is an option if simply overriding individual
config files isn't enough.

This module is also great if the desire is to only alter a very small portion of
an existing config file. Sometimes only a single line of config in a file with
hundreds of lines of config is needed. Rather than lose all update to that file
in the future when it is overridden, Config Actions allows single parts to be
changed.

Documentation can be found here: [http://config-actions.readthedocs.io/en/latest/index.html](http://config-actions.readthedocs.io/en/latest/index.html)

[An Override Example](http://config-actions.readthedocs.io/en/latest/example_override.html)
