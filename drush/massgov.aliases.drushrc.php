<?php

$env_vars = [
  '#env-vars' => ['ETC_PREFIX' => '/dev/null'],
];

// Site massgov, environment cd
$aliases['cd'] = array(
    'root' => '/var/www/html/massgov.cd/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'cd',
    'ac-realm' => 'prod',
    'uri' => 'massgovcd.prod.acquia-sites.com',
    'remote-host' => 'massgovcd.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.cd',
  ) + $env_vars;
$aliases['cd.livedev'] = array(
    'parent' => '@massgov.cd',
    'root' => '/mnt/gfs/massgov.cd/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment dev
$aliases['dev'] = array(
    'root' => '/var/www/html/massgov.dev/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'dev',
    'ac-realm' => 'prod',
    'uri' => 'massgovdev.prod.acquia-sites.com',
    'remote-host' => 'massgovdev.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.dev',
  ) + $env_vars;
$aliases['dev.livedev'] = array(
    'parent' => '@massgov.dev',
    'root' => '/mnt/gfs/massgov.dev/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment feature1
$aliases['feature1'] = array(
    'root' => '/var/www/html/massgov.feature1/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'feature1',
    'ac-realm' => 'prod',
    'uri' => 'massgovfeature1.prod.acquia-sites.com',
    'remote-host' => 'massgovfeature1.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.feature1',
  ) + $env_vars;
$aliases['feature1.livedev'] = array(
    'parent' => '@massgov.feature1',
    'root' => '/mnt/gfs/massgov.feature1/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment feature2
$aliases['feature2'] = array(
    'root' => '/var/www/html/massgov.feature2/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'feature2',
    'ac-realm' => 'prod',
    'uri' => 'massgovfeature2.prod.acquia-sites.com',
    'remote-host' => 'massgovfeature2.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.feature2',
  ) + $env_vars;
$aliases['feature2.livedev'] = array(
    'parent' => '@massgov.feature2',
    'root' => '/mnt/gfs/massgov.feature2/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment feature3
$aliases['feature3'] = array(
    'root' => '/var/www/html/massgov.feature3/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'feature3',
    'ac-realm' => 'prod',
    'uri' => 'massgovfeature3.prod.acquia-sites.com',
    'remote-host' => 'massgovfeature3.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.feature3',
  ) + $env_vars;
$aliases['feature3.livedev'] = array(
    'parent' => '@massgov.feature3',
    'root' => '/mnt/gfs/massgov.feature3/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment feature4
$aliases['feature4'] = array(
    'root' => '/var/www/html/massgov.feature4/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'feature4',
    'ac-realm' => 'prod',
    'uri' => 'massgovfeature4.prod.acquia-sites.com',
    'remote-host' => 'massgovfeature4.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.feature4',
  ) + $env_vars;
$aliases['feature4.livedev'] = array(
    'parent' => '@massgov.feature4',
    'root' => '/mnt/gfs/massgov.feature4/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment feature5
$aliases['feature5'] = array(
    'root' => '/var/www/html/massgov.feature5/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'feature5',
    'ac-realm' => 'prod',
    'uri' => 'massgovfeature5.prod.acquia-sites.com',
    'remote-host' => 'massgovfeature5.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.feature5',
  ) + $env_vars;
$aliases['feature5.livedev'] = array(
    'parent' => '@massgov.feature5',
    'root' => '/mnt/gfs/massgov.feature5/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment prod
$aliases['prod'] = array(
    'root' => '/var/www/html/massgov.prod/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'prod',
    'ac-realm' => 'prod',
    'uri' => 'massgov.prod.acquia-sites.com',
    'remote-host' => 'massgov.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.prod',
  ) + $env_vars;
$aliases['prod.livedev'] = array(
    'parent' => '@massgov.prod',
    'root' => '/mnt/gfs/massgov.prod/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment ra
$aliases['ra'] = array(
    'root' => '/var/www/html/massgov.ra/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'ra',
    'ac-realm' => 'prod',
    'uri' => 'massgovra.prod.acquia-sites.com',
    'remote-host' => 'massgovra.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.ra',
  ) + $env_vars;
$aliases['ra.livedev'] = array(
    'parent' => '@massgov.ra',
    'root' => '/mnt/gfs/massgov.ra/livedev/docroot',
  ) + $env_vars;

// Site massgov, environment test
$aliases['test'] = array(
    'root' => '/var/www/html/massgov.test/docroot',
    'ac-site' => 'massgov',
    'ac-env' => 'test',
    'ac-realm' => 'prod',
    'uri' => 'massgovstg.prod.acquia-sites.com',
    'remote-host' => 'massgovstg.ssh.prod.acquia-sites.com',
    'remote-user' => 'massgov.test',
  ) + $env_vars;
$aliases['test.livedev'] = array(
    'parent' => '@massgov.test',
    'root' => '/mnt/gfs/massgov.test/livedev/docroot',
  ) + $env_vars;
