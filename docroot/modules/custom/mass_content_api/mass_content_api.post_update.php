<?php

/**
 * @file
 * Post update functions for Mass Content API.
 */

/**
 * Queue items to check for descendants.
 *
 * Nodes will be queued to update the descendant information when they are
 * updated, deleted, or added. This update will queue everything since this
 * functionality is new.
 */
function mass_content_api_post_update_descendant_populate() {
  $queue = \Drupal::queue('mass_content_api_descendant_queue');
  $descendant_manager = \Drupal::getContainer()->get('descendant_manager');

  $relationships = $descendant_manager->getRelationshipConfiguration();

  $query = \Drupal::entityQuery('node');
  $query->condition('status', 1)
    ->condition('type', array_keys($relationships), 'IN');

  $results = $query->execute();

  foreach ($results as $result) {
    $queue->createItem((object) ['id' => $result]);
  }
}
