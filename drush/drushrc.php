<?php


/**
 * Using the flag "--structure-tables-key=common" on sql-dump and sql-sync will cause
 * the structure but not the data to be dumped for these tables.
 */
$options['structure-tables']['common'] = array('cache', 'cache_*', 'history', 'search_*', 'sessions', 'watchdog');

$command_specific['sql-sync'] = [
  'structure-tables-key' => 'common',
];

# @todo - 'root' can't be set in a drushrc. for now run drush comamnds from docroot.
# this is fixed in drush9.
// $options['root'] = __DIR__ . "/docroot";
$options['uri'] = "http://mass.local";
