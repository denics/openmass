<?php

$databases = array();

$settings['hash_salt'] = '${drupal.hash_salt}';
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

$settings['file_public_path'] = '${drupal.settings.file_public_path}';
$settings['file_private_path'] = '${drupal.settings.file_private_path}';

// Configure the temp directory.
$config['system.file']['path']['temporary'] = "/mnt/gfs/{$_ENV['AH_SITE_GROUP']}.{$_ENV['AH_SITE_ENVIRONMENT']}/tmp";

$settings['trusted_host_patterns'] = array(
  '^${acquia.accountname}dev.prod.acquia-sites.com',
  '^${acquia.accountname}stg.prod.acquia-sites.com',
  '^${acquia.accountname}cd.prod.acquia-sites.com',
  '^pilot\.mass\.gov$',
);

// Include the Acquia database connection and other config.
if (file_exists('/var/www/site-php')) {
  require '/var/www/site-php/${acquia.accountname}/${acquia.accountname}-settings.inc';
}

// Include deployment identifier to invalidate internal twig cache.
if (file_exists($app_root . '/' . $site_path . '/deployment_id.php')) {
  require $app_root . '/' . $site_path . '/deployment_id.php';
}

// Use our own config sync directory.
$config_directories = array();
$config_directories[CONFIG_SYNC_DIRECTORY] = '${drupal.config_sync_directory}';

//// Add an htaccess prompt on dev.
//// @see https://docs.acquia.com/articles/password-protect-your-non-production-environments-acquia-hosting#phpfpm

// Make sure Drush keeps working.
// Modified from function drush_verify_cli()
$cli = (php_sapi_name() == 'cli');

// PASSWORD-PROTECT NON-PRODUCTION SITES (i.e. staging/dev)
if (!$cli && (isset($_ENV['AH_NON_PRODUCTION']) && $_ENV['AH_NON_PRODUCTION'])) {
  $username = 'massgov';
  $password = 'for the commonwealth';
  if (!(isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_USER']==$username && $_SERVER['PHP_AUTH_PW']==$password))) {
    header('WWW-Authenticate: Basic realm="This site is protected"');
    header('HTTP/1.0 401 Unauthorized');
    // Fallback message when the user presses cancel / escape
    echo 'Access denied';
    exit;
  }
}

// IP-PROTECT PRODUCTION SITE
// uncomment to enforce IP restrictions in PROD
/*if (!$cli && isset($_ENV['AH_SITE_ENVIRONMENT']) && 'prod' == $_ENV['AH_SITE_ENVIRONMENT']) {
  // All IPs must be in CIDR format, including single address IPs.
  $ips = array(
    '10.20.0.0/16',     // Virtual machine addresses
    '146.243.0.0/16',   // MassIT VPN
    '170.63.0.0/16',    // MassIT VPN TODO: this currently includes MASSIT WIFI
    '65.204.38.243/32', // MassIT VPN
    '65.204.38.3/32',   // MassIT VPN
    '63.250.249.138/32',// Palantir VPN
    '104.247.39.34/32', // From here to end are Acquia internal
    '40.130.238.138/32',
    '207.173.24.186/32',
    '50.224.63.14/32',
    '80.71.2.77/32',
    '66.207.219.134/32',
    '208.66.24.54/32',
    '50.247.79.241/32',
    '59.100.22.81/32',
    '14.141.169.186/32',
  );
  // Override restrict_by_ip configuration in these environments.
  // If no override, IP restrictions apply as set in config or GUI,
  // which should usually be empty string unless testing.
  $config['restrict_by_ip.settings']['login_range'] = implode(';',$ips);

  // Get IP address from load balancer, and tell restrict_by_ip to use it.
  // $_SERVER['AH_CLient_IP'] is not expected to be set at this point, but just in case.
  if (empty($_SERVER['AH_Client_IP'])) {
    // Default value if Acquia environment variable is not available.
    $_SERVER['AH_Client_IP'] = $_SERVER['REMOTE_ADDR'];
    // Environment value set by Acquia.
    if (!empty($_ENV['AH_Client_IP'])) {
      $_SERVER['AH_Client_IP'] = $_ENV['AH_Client_IP'];
    }
  }
  $config['restrict_by_ip.settings']['header'] = 'AH_Client_IP';
}*/

