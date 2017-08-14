# Best Practices

## Namespaced Prefix

### In Sub Profiles
All machine names (for content types, views, image styles, etc...) should be
prefixed with an appropriate namespace such as `ucsf_`. This ensures that there
are no conflicts with future updates or customizations made my end users.

It also allows those items to be locked down so that they can not be altered by
end users. This is achieved by adding that prefix to the
`lock_sitefarm_features.settings.yml` config file.

### In SiteFarm Seed
All machine names within SiteFarm Seed should use the `sf_` prefix.

## Composer

Composer should ony be run from a distribution's root directory. Never run
composer in a module or profile. This will try to download dependencies and
modules into that module or profile. In the case of a profile, it will actually
download a copy of Drupal Core into the profile. So. Only run composer in the
distribution's root directory.

Commit the `composer.lock` file in the distributions root directory. This will
ensure that everyone gets the exact same modules and php packages every time
they do a `$ composer install`.

### Updating modules
When updating modules, it is best to do one module at a time rather than doing
a wholesale `$ composer update`. So to update the metatag module just run
`$ composer update drupal/metatag`. If a module or php package has dependencies
that need updating as well, add the `--with-dependencies` parameter.

## Drupal Console

Use [Drupal Console](https://drupalconsole.com/) for generating code for
everything from modules to themes.

It is also useful for finding available services when doing dependency injection
into classes. From the Drupal root `web` directory run:

```
$ ../vendor/bin/drupal container:debug
```
## Overriding Services

SiteFarm Seed provides several Services which can be overridden. This is useful especially for services used in procedual hooks within areas like the `sitefarm_core.module` file.

By moving all the code in hooks into services it is possible for a subprofile to swap out the service with one of their own. This means that everything inside a `.module` file can be undone if needed.

This can be more useful than simply turning off a module or overriding it. The base class can be extended and then only the methods that need to be changed can be overridden. This way changes can be more surgical, and the original code can still be used when desired.

https://api.drupal.org/api/drupal/core!core.api.php/group/container/8.2.x#sec_injection

## Style switching with Block Style Plugins

The [Block Style Plugins](https://www.drupal.org/project/block_style_plugins) module is designed to allow day-to-day editors the
ability to swap styles per block. A themer can designate which styles apply to
which blocks. This keeps style and data separate so that a theme can be switched
and offer its own styles.

Tutorial Video: [https://youtu.be/Y0t8owlV2_4](https://youtu.be/Y0t8owlV2_4)

## Testing

Always test out new configuration by creating a brand new site installation. To
speed this along, it helps to have a drush or drupal console command ready to
go. From the Drupal root `web` directory run:

```
$ ../vendor/bin/drush site-install sitefarm_subprofile --account-name=janedoe --account-pass=mypassword --site-name="SiteFarm Seed Subprofile"
```

### Testing updates to existing sites
Before you start adding new features, create a database backup first so that it
can be used for testing later. It is helpful to have an older database backup of
a fresh install. This way it is easier to see if the most recent changes take
place without the cruft of a live site's backup. So before you start adding new
features, create a database backup of a fresh install first. Run Behat tests.

Load in a database backup of an existing site and run updates with `drush updb`.
Then look to see that all config updates have applied and then run Behat tests.

### Behavioral Testing with Behat
It is recommended to write a Behat tests to verify that the desired effects from
a configuration change have applied correctly.

### Unit tests with PHPUnit
Every public method in a PHP class or Service should have a unit test to ensure
that it works properly
