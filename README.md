# Behat Testing for SiteFarm

## Local Machine Setup
1. Copy behat.template.yml to behat.yml and modify the base_url to your local site's url.
```
$ cp behat.template.yml behat.yml
```

2. Download and install the Selenium Driver.
Get the latest version of Selenium Server at [http://docs.seleniumhq.org/download/](http://docs.seleniumhq.org/download/) (currently 2.53.0).

Start your Selenium server in a new console window (change the path to reflect where you downloaded it):
```
$ java -jar /path/to/selenium-server-standalone-2.53.0.jar
```

> **Tip:** Add an alias to your `.bash_profile` to simplify life
> `alias selenium="java -jar /Applications/selenium-server-standalone-2.53.0.jar"`

3. Run behat from this directory:
```
$ cd {PROFILES}/sitefarm
$ /path/to/MYPROJECT/vendor/bin/behat
```
or
```
$ ../../../vendor/bin/behat
```

### Using Chrome Browser to run tests
By default, Firefox is used to run Selenium tests. However, you can choose to use Chrome instead.

1. Uncomment the following lines in your behat.yml file.
```
selenium2:
  browser: chrome
  capabilities: {"browser": "chrome", "browserName": "chrome", "browserVersion": "ANY", "version": "ANY"}
```

2. Download the Chrome driver [https://sites.google.com/a/chromium.org/chromedriver/downloads](https://sites.google.com/a/chromium.org/chromedriver/downloads)

3. Start Selenium using the Chrome driver (change the path to reflect where you downloaded Selenium and Chrome driver):
```
$ java -jar /path/to/selenium-server-standalone-2.53.0.jar -Dwebdriver.chrome.driver=/path/to/chromedriver
```

4. Run behat as usual.


## Running behat

From the `/docroot/profiles/sitefarm` directory, you can run all tests using the default profile:
```
$ /path/to/MYPROJECT/vendor/bin/behat
```

Or run one test by name:
```
$ /path/to/MYPROJECT/vendor/bin/behat features/featurename.feature
```

## Grouping with Tags
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

`@chrome` - Any test that absolutely requires the Chrome browser. These test will be ignored unless they are explicitly run via the chrome profile `$ /path/to/MYPROJECT/vendor/bin/behat --profile chrome`

> **Tip:** during development, add a tag `@current` so that only the test you want can be run with `$ /path/to/MYPROJECT/vendor/bin/behat --tags @current`
> Even better, add an alias to your `.bash_profile` to speed things up.
> `alias bhc='/path/to/MYPROJECT/vendor/bin/behat --tags @current'` for "BeHat Current"
