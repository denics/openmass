<?php


/**
 * Using the flag "--structure-tables-key=common" on sql-dump and sql-sync will cause
 * the structure but not the data to be dumped for these tables.
 */
$options['structure-tables']['common'] = array('cache', 'cache_*', 'history', 'search_*', 'sessions', 'watchdog');

$command_specific['sql-sync'] = [
  'structure-tables-key' => 'common',
];

$command_specific['core-rsync'] = [
  'exclude-paths' => 'css:js:styles:config*:php',
];

# @todo Typically, 'root' can't be set in a drushrc. This file gets included via --config by a Drush launcher script in the VM.
# @todo Replace that launcher script with drush-shim - https://github.com/webflo/drush-shim
$options['root'] = dirname(__DIR__) . "/docroot";

$options['uri'] = "http://mass.local";
