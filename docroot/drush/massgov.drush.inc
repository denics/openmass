<?php

use Acquia\Cloud\Api\CloudApiClient;


function massgov_drush_command() {
  $items['ma-deploy'] = array(
    'description' => 'Deploy code and database (if needed). Copies Prod DB to target environment and then runs config import, updb, varnish purge, etc.',
    'arguments' => array(
      'target' => 'Target environment. Recognized values: dev, cd, test, feature1, feature2, feature3',
      'git-ref' => 'Tag or branch to deploy. Must be pushed to Acquia.',
    ),
    'required-arguments' => TRUE,
    'options' => array(
      'refresh-db' => 'Run sql-sync & pull the db from prod to replace target environment.',
      'skip-maint' => 'Skip maintenance mode enable/disable.',
    ),
    'drush dependencies' => array('acapi'),
    'bootstrap' => DRUSH_BOOTSTRAP_NONE,
    'examples' => array(
      'drush ma-deploy test tags/build-0.6.1' => 'Deploy tag to staging environment.',
    ),
  );
  return $items;
}

function drush_massgov_ma_deploy_validate($target) {
  $available_targets = array('dev', 'cd', 'test', 'feature1', 'feature2', 'feature3');
  if (!in_array($target, $available_targets)) {
    return drush_set_error('Invalid argument: target. \nYou entered "'.$target.'". Target must be one of: '.join(', ',$available_targets));
  }
}

function drush_massgov_ma_deploy($target, $git_ref) {
  $alias_target =  "@massgov.$target";
  $site = 'prod:massgov';
  $db_name = 'massgov';

  // Build Cloud API client connection.
  $cloudapi = CloudApiClient::factory(array(
    // Easiest way to provide creds is to run `drush ac-api-login` once, before running ma-deploy
    //
    // These acapi_get_option() calls are the only dependency on acapi.drush.inc and
    // the ~/.acquia/cloudapi.conf file. We could remove the dependency.
    'username' => acapi_get_option('email'),
    'password' => acapi_get_option('key'),
  ));


  if (drush_get_option('skip-maint', FALSE) == FALSE) {
    // Turn on Maint mode
    $args = array('system.maintenance_mode', 1);
    $options = array('format' => 'integer');
    $return = drush_invoke_process($alias_target, 'state-set', $args, $options);
    massgov_check_drush_error($return);
    drush_log(dt("Maintenance mode enabled in $target."), 'success');
  }

  $files = $cloudapi->copyFiles($site, 'prod', $target);

  if (drush_get_option('refresh-db', FALSE)) {
    $database = $cloudapi->copyDatabase($site, $db_name, 'prod', $target);
    // Copy Prod files to target.
    $files = $cloudapi->copyFiles($site, 'prod', $target);

    $id = $database->id();
    wait_for_task_to_complete($cloudapi, $site, $id);
  }


  // Deploy the new code.
  $code = $cloudapi->pushCode($site, $target, $git_ref);
  $id = $code->id();
  wait_for_task_to_complete($cloudapi, $site, $id);

  // Run any pending DB updates
  // This goes before config import per https://www.drupal.org/node/2628144
  $options = array('entity-updates' => TRUE);
  $return = drush_invoke_process($alias_target, 'updb', $args, $options);
  massgov_check_drush_error($return);
  drush_log(dt("Database and entity updates completed in $target."), 'success');

  // Import new config
  $return = drush_invoke_process($alias_target, 'config-import', []);
  massgov_check_drush_error($return);
  drush_log(dt("Configuration imported in $target."), 'success');

  // Purge Varnish cache.
  // Get a list of all an environment's domains.
  $domains = $cloudapi->domains($site, $target);
  foreach ($domains as $domain) {
    $cloudapi->purgeVarnishCache($site, $target, $domain);
    drush_log(dt("Purged Varnish cache for $domain in $target environment."), 'success');
  }

  // @todo Disable System Notification block

  if (drush_get_option('skip-maint', FALSE)  == FALSE) {
    // Disable Maintenance mode.
    $args = array('system.maintenance_mode', 0);
    $options = array('format' => 'integer');
    $return = drush_invoke_process($alias_target, 'state-set', $args, $options);
    massgov_check_drush_error($return);
    drush_log(dt("Maintenance mode disabled in $target."), 'success');
  }
}

/**
 * Pause until a given task is completed.
 *
 * This function handles Cloud API 503 errors, and will ignore up to five 503s
 * before failing.
 *
 * @param \Acquia\Cloud\Api\CloudApiClient $cloudapi
 * @param $site
 * @param int $id
 *   The task ID.
 * @return bool
 * @throws \Exception
 */
function wait_for_task_to_complete(CloudApiClient $cloudapi, $site, $id) {
  $task_complete = FALSE;
  $cloud_api_failures = 0;

  while ($task_complete !== TRUE) {
    try {
      $task_status = $cloudapi->task($site, $id);
      if ($task_status->state() == 'done') {
  $task_complete = TRUE;
  drush_log(dt('!desc is complete: Task !task_id.', array('!desc' => $task_status->description(), '!task_id' => $id)), 'success');
      }
      elseif ($task_status->state() == 'failed') {
  throw new Exception(dt("!desc - Task !task_id failed:\n!logs", array('!desc' => $task_status->description(), '!task_id' => $id, $task_status->logs())));
      }
      else {
  drush_log(dt('!desc: Will re-check Task !task_id for completion in 5 seconds.', array('!desc' => $task_status->description(), '!task_id' => $id)), 'notice');
  sleep(5);
      }
    } catch (Guzzle\Http\Exception\ServerErrorResponseException $e) {
      if ($e->getCode() == 503) {
  $cloud_api_failures++;
  if ($cloud_api_failures >= 5) {
    throw new Exception('Cloud API returned 5 or more 503s, indicating failure to complete.');
  }
      }
    }
  }
  return $task_complete;
}

/**
 * Throws an exception if a remote Drush call experiened an error.
 *
 * @param $return
 *  A return object from drush_invoke_process().
 * @throws \Exception
 */
function massgov_check_drush_error($return) {
  if (!$return) {
    throw new Exception('Failure in drush_invoke_process()');
  }
  elseif ($return['error_status']) {
    foreach ($return['error_log'] as $key => $value) {
      foreach ($value as $message) {
  $messages[] = $message;
      }
    }
    throw new Exception(implode('', $messages));
  }
}
