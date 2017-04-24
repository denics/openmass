<?php
/**
 * @file
 * Project-specific drush configuration.
 *
 * This file can be used with `drush -c conf/drushrc.php`
 */

exec('git rev-parse --show-toplevel 2> /dev/null', $output);
if (!empty($output)) {
  $repo = current($output);

  # Configure Drush for the current project.
  $options['root'] = "{$repo}/docroot";
  $options['uri'] = "http://mass.local";
  $options['include'] = [
    // @todo Not needed after https://github.com/previousnext/drush_cmi_tools/issues/7
    "{$repo}/vendor/previousnext/drush_cmi_tools",
    "{$repo}/artifacts/acquiacloud/.drush",
  ];
  // @todo Remove after this code is on Prod.
  $options['alias-path'] = "{$repo}/artifacts/acquiacloud/.drush";
}

/**
 * Using the flag "--structure-tables-key=common" on sql-dump and sql-sync will cause
 * the structure but not the data to be dumped for these tables.
 */
$options['structure-tables']['common'] = array('cache', 'cache_*', 'history', 'search_*', 'sessions', 'watchdog');
