# Configuring Your Localhost

## Step 1: Install Composer

[Install composer globally.](https://getcomposer.org/doc/00-intro.md) 

## Step 2: Fire Up Local Dev Stack

Turn on MAMP, WAMP, LAMP, or what ever you local development environment of choice and make sure the [Web Server System Requirements](https://www.drupal.org/docs/8/system-requirements/web-server) for Drupal 8 are being met.

## Step 3: Get Distro Template

This repo is just meant as a starting point. There is no plan to maintain and update it.

Next you will want to "Clone or Download" the [SiteFarm Distro Template](https://github.com/ucdavis/sitefarm-distro-template) into your local servers public directory. If you clone, the root directory will be `sitefarm-distro-template`, which is what will be referenced in the rest of this document. 

If cloning you will need to configure an [SSH Key with GitHub](https://help.github.com/articles/adding-a-new-ssh-key-to-your-github-account/).

## Step 4: Configure Your Local Host

You will want to set up a local host on your local PHP/MySQL tool of choice. Give it a name like `sitefarm-distro.local` and set the document root to `sitefarm-distro-template/web`

## Step 5: Boss Composer Around

Open your CLI of choice and navigate to the SiteFarm Distro Template directory (`sitefarm-distro-template`).

Run, `$ composer install`

Composer will download and install the SiteFarm install profile. You can then install it like you would any other Drupal site.

## Step 6: Add a Database

Using a tool like [Sequel Pro](https://www.sequelpro.com/) or [phpMyAdmin](https://www.phpmyadmin.net/) create a local database.

## Step 7: Install Drupal

Before installing drupal you may want to increase your `memory_limit` in your `php.ini` file. I like to bump it up to `520M`. You may also want to increase your `post_max_size` to `120M`. This will reduce the likelihood of errors on install.

Open the SiteFarm Distro Template's web directory(`sitefarm-distro-template/web`) in your browser or use the local host name, my example was `sitefarm-distro.local` and follow the instructions to install Drupal.

## Local Dev Tips

* Use “Drush” in a console from the `web` directory with:
  $ ../vendor/bin/drush
* Log in as an Administrator:
    * $ ../vendor/bin/drush uli
    * copy the url after `http://default/` and paste it after your local host name in the browser
* Use “Drupal Console” in a console from the docroot directory with:
  $ ../vendor/bin/drupal