<?php

$options['include'] = [
  // @todo Not needed after https://github.com/previousnext/drush_cmi_tools/pull/8
  "../vendor/previousnext/drush_cmi_tools",
];
/**
 * Using the flag "--structure-tables-key=common" on sql-dump and sql-sync will cause
 * the structure but not the data to be dumped for these tables.
 */
$options['structure-tables']['common'] = array('cache', 'cache_*', 'history', 'search_*', 'sessions', 'watchdog');

$command_specific['sql-sync'] = [
  'structure-tables-key' => 'common',
];

$command_specific['config-import-plus'] = [
  'source' => '../conf/drupal/config',
  'delete-list' => '../conf/drupal/delete.yml'
];
