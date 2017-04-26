<?php

if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}
// Site massgov, environment cd
$aliases['cd'] = array(
  'root' => '/var/www/html/massgov.cd/docroot',
  'ac-site' => 'massgov',
  'ac-env' => 'cd',
  'ac-realm' => 'prod',
  'uri' => 'massgovcd.prod.acquia-sites.com',
  'remote-host' => 'staging-21435.prod.hosting.acquia.com',
  'remote-user' => 'massgov.cd',
  'path-aliases' => array(
    '%drush-script' => 'drush' . $drush_major_version,
  )
);
$aliases['cd.livedev'] = array(
  'parent' => '@massgov.cd',
  'root' => '/mnt/gfs/massgov.cd/livedev/docroot',
);

if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}
// Site massgov, environment dev
$aliases['dev'] = array(
  'root' => '/var/www/html/massgov.dev/docroot',
  'ac-site' => 'massgov',
  'ac-env' => 'dev',
  'ac-realm' => 'prod',
  'uri' => 'massgovdev.prod.acquia-sites.com',
  'remote-host' => 'staging-21435.prod.hosting.acquia.com',
  'remote-user' => 'massgov.dev',
  'path-aliases' => array(
    '%drush-script' => 'drush' . $drush_major_version,
  )
);
$aliases['dev.livedev'] = array(
  'parent' => '@massgov.dev',
  'root' => '/mnt/gfs/massgov.dev/livedev/docroot',
);

if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}
// Site massgov, environment prod
$aliases['prod'] = array(
  'root' => '/var/www/html/massgov.prod/docroot',
  'ac-site' => 'massgov',
  'ac-env' => 'prod',
  'ac-realm' => 'prod',
  'uri' => 'massgov.prod.acquia-sites.com',
  'remote-host' => 'web-21429.prod.hosting.acquia.com',
  'remote-user' => 'massgov.prod',
  'path-aliases' => array(
    '%drush-script' => 'drush' . $drush_major_version,
  )
);
$aliases['prod.livedev'] = array(
  'parent' => '@massgov.prod',
  'root' => '/mnt/gfs/massgov.prod/livedev/docroot',
);

if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}
// Site massgov, environment test
$aliases['test'] = array(
  'root' => '/var/www/html/massgov.test/docroot',
  'ac-site' => 'massgov',
  'ac-env' => 'test',
  'ac-realm' => 'prod',
  'uri' => 'massgovstg.prod.acquia-sites.com',
  'remote-host' => 'staging-21435.prod.hosting.acquia.com',
  'remote-user' => 'massgov.test',
  'path-aliases' => array(
    '%drush-script' => 'drush' . $drush_major_version,
  )
);
$aliases['test.livedev'] = array(
  'parent' => '@massgov.test',
  'root' => '/mnt/gfs/massgov.test/livedev/docroot',
);
