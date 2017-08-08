# Configuring Your Localhost

## Step 1: Install Composer

[Install composer globally.](https://getcomposer.org/doc/00-intro.md) 

## Step 2: Fire Up Local Dev Stack

Turn on MAMP, WAMP, LAMP, or what ever you local development environment of choice and make sure the [Web Server System Requirements](https://www.drupal.org/docs/8/system-requirements/web-server) for Drupal 8 are being met.

## Step 3: Get Distro Template

Next you will want to "Clone or Download" the [SiteFarm Distro Template](https://github.com/ucdavis/sitefarm-distro-template) into your local servers public directory.

## Step 4: Boss Composer Around

Open your CLI of choice and navigate to the SiteFarm Distro Template directory.

Run, `$ composer install`

Composer will download and install the SiteFarm install profile. You can then install it like you would any other Drupal site.

## Step 5: Install Drupal

Open the SiteFarm Distro Template directory in your browser and follow the instructions to install Drupal.