# Behat Testing for SiteFarm Seed

General Behat documentation can be found at [http://behat.org/en/latest/guides.html](http://behat.org/en/latest/guides.html).

## Local Machine Setup
1. Copy [behat.template.yml](../behat.template.yml) to behat.yml and modify the base_url to your local site's url.
```
$ cp behat.template.yml behat.yml
```

2. Download and install the Selenium Driver.
Get the latest version of Selenium Server at [http://docs.seleniumhq.org/download/](http://docs.seleniumhq.org/download/) (currently 3.4.0).

Selenium requires 


### Using Chrome Browser to run tests
1. Download the Chrome driver [https://sites.google.com/a/chromium.org/chromedriver/downloads](https://sites.google.com/a/chromium.org/chromedriver/downloads)

2. Start Selenium using the Chrome driver in a new console window (change the path to reflect where you downloaded Selenium and Chrome driver):
```
$ java -Dwebdriver.chrome.driver="/path/to/chromedriver" -jar /path/to//selenium-server-standalone-3.4.0.jar
```

> **Tip:** Add an alias to your `.bash_profile` to simplify life
> `alias selenium='java -Dwebdriver.chrome.driver="/path/to/chromedriver" -jar /path/to/selenium-server-standalone-3.4.0.jar'`

> **Tip:** If you get an error when running tests like `Could not open connection: Unable to create new service: ChromeDriverService`, then it is probably an isse with the Chrome Driver not being found. The Chrome driver path needs to be absolute when starting Selenium. 

3. Run behat as usual.


## Running behat

Behat tests need to be run from sitefarm_seed profile directory:
```
$ cd {PROFILES}/sitefarm_seed
```
Then run all behat tests in the default profile

```
$ /path/to/MYPROJECT/vendor/bin/behat
```
or
```
$ ../../../vendor/bin/behat
```

### Run only tests in a specific file
Specify a specific test "feature" filename:
```
$ /path/to/MYPROJECT/vendor/bin/behat tests/features/featurename.feature
```

### Grouping with Tags
Behat tests can be tagged into groups. Tags affect what options from the config file are in effect. You can also use tags to run only a subset of the available tests.

For example, to include only tests that are tagged as 'javascript':
```
$ /path/to/MYPROJECT/vendor/bin/behat --tags @javascript
```

To exclude tests that are tagged as 'javascript':
```
$ /path/to/MYPROJECT/vendor/bin/behat --tags ~@javascript
```

`@api` - Any test that requires anything more than blackbox access (for example, any test that starts by creating a user and logging them in) should be tagged with `@api` so it can use drush or get direct access to internal Drupal functions.

`@javascript` - Any test that requires browser interaction (for example, WYSIWYG editing or other Javascript functions) should be tagged with `@javascript`.

`@local_files` - Any test that requires files stored in the `{PROFILES}/sitefarm/tests/files/` directory should be tagged with `@local_files`.

> **Tip:** during development, add a tag `@current` so that only the test you want can be run with `$ /path/to/MYPROJECT/vendor/bin/behat --tags @current`
> Even better, add an alias to your `.bash_profile` to speed things up.
> `alias bhc='/path/to/MYPROJECT/vendor/bin/behat --tags @current'` for "BeHat Current"

# PHPUnit for Unit tests

PHPUnit is used for unit testing each class. Unit tests ensure that each class and method does exactly what it is meant to do.

General PHPUnit documentation can be found at [https://phpunit.de/documentation.html](https://phpunit.de/documentation.html).

Drupal specific PHPUnit testing documentation can be found at [https://www.drupal.org/docs/8/phpunit](https://www.drupal.org/docs/8/phpunit).

## Running PHPUnit

PHPUnit tests need to be run from sitefarm_seed profile directory:
```
$ cd {PROFILES}/sitefarm_seed
```
Then run all unit tests in the sitefarm_seed profile

```
$ /path/to/MYPROJECT/vendor/bin/phpunit
```
or
```
$ ../../../vendor/bin/phpunit
```

### Run only tests in a specific group
Groups in PHPUnit are specified by a `@group` Annotation. For example, to run all of the "sitefarm_core" tests:
```
$ /path/to/MYPROJECT/vendor/bin/phpunit --group sitefarm_core
```
