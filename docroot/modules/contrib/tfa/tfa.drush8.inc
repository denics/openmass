<?php

/**
 * @file
 * Drush integration for TFA.
 */

/**
 * Implements hook_sql_sync_sanitize().
 */
function tfa_drush_sql_sync_sanitize() {
  $sql = "DELETE FROM users_data WHERE LEFT(name, 4) = 'tfa_'";
  drush_sql_register_post_sync_op('tfa', dt('Remove recovery codes and other user-specific TFA data.'), $sql);
}
