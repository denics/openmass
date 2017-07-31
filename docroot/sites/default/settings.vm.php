<?php

use Drupal\KernelTests\Core\Entity\EntityQueryTest;
use Drupal\node\Entity\Node;

$databases = array();
$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => getenv('DB_DB') ?: 'drupal',
  'username' => getenv('DB_USER') ?: 'root',
  // Allow for setting an empty password via environment.
  'password' => getenv('DB_PASS') === FALSE ? 'root' : getenv('DB_PASS'),
  'port' => getenv('DB_PORT') ?: 3306,
  'host' => getenv('DB_HOST') ?: '127.0.0.1',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
);

$config_directories = array();
$config_directories[CONFIG_SYNC_DIRECTORY] = '../conf/drupal/config';

$settings['hash_salt'] = 'temporary';
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '';

// Include the Acquia database connection and other config.
if (file_exists('/var/www/site-php')) {
  $settings['file_public_path'] = 'files';
  $settings['file_private_path'] = "/mnt/files/{$_ENV['AH_SITE_GROUP']}.{$_ENV['AH_SITE_ENVIRONMENT']}/files-private";
  $settings['trusted_host_patterns'] = array(
    'massgovdev.prod.acquia-sites.com',
    'massgovstg.prod.acquia-sites.com',
    'massgovcd.prod.acquia-sites.com',
    '^pilot\.mass\.gov$',
  );

  // Include deployment identifier to invalidate internal twig cache.
  if (file_exists($app_root . '/sites/deployment_id.php')) {
    require $app_root . '/sites/deployment_id.php';
  }

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

  require '/var/www/site-php/${acquia.accountname}/${acquia.accountname}-settings.inc';
}
