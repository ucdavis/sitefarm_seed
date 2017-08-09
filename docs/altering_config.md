# Adding New Configuration in a Sub Profile

## General



### Individual File Export



### Features Module


## Altering Content Types

## Altering Fields on existing content types

## Altering Views

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
