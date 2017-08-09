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

## Testing

Always test out new configuration by creating a brand new site installation. To
speed this along, it helps to have a drush or drupal console command ready to
go. From the Drupal root `web` directory run:

```
$ ../vendor/bin/drush site-install sitefarm_subprofile --account-name=janedoe --account-pass=mypassword --site-name="SiteFarm Seed Subprofile"
```

### Testing updates to existing sites
Load in a database backup of an existing site and run updates with `drush updb`.
Then look to see that all config updates have applied and run Behat tests.

### Behavioral Testing with Behat
It is recommended to write a Behat test to verify that the desired effects from
a configuration change have applied correctly.

### Unit tests with PHPUnit
Every public method in a PHP class or Service should have a unit test to ensure
that it works properly
