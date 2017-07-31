<?php
// docroot/sites/default/settings.php

// see: https://docs.acquia.com/acquia-cloud/develop/env-variable
// for why this is first.
if (file_exists('/var/www/site-php')) {
  require "/var/www/site-php/massgov/massgov-settings.inc";
}

// Include deployment identifier to invalidate internal twig cache.
if (file_exists($app_root . '/sites/deployment_id.php')) {
  require $app_root . '/sites/deployment_id.php';
}

// If in an Acquia Cloud environment
if(isset($_ENV['AH_SITE_ENVIRONMENT'])) {
  // if in acquia...
  require $app_root . '/' . $site_path . '/settings.acquia.php';

  // If there is a need in the future to have a production configuration
  // if also prod...
  // if(isset($_ENV['AH_PRODUCTION']) {
  //   require $app_root . '/' . $site_path . '/settings.acquia-prod.php';
  // }
} else {
  // if not in Acquia
  require $app_root . '/' . $site_path . '/settings.vm.php';

  // Override as needed via a settings.local.php. Use docroot/sites/example.settings.local.php as a model.
  if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
    include $app_root . '/' . $site_path . '/settings.local.php';
  }
}
