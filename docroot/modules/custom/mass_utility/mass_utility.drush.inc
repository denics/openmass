<?php

/**
 * @file
 * Contains custom drush commands for mass.gov.
 */

use Drupal\node\Entity\Node;

/**
 * Implements hook_drush_command().
 */
function mass_utility_drush_command() {
  $items = [];
  $items['mass-topic-page-type-update'] = [
    'description' => 'Update Topic Page nodes to use default topic type',
    'drupal dependencies' => ['mass_utility'],
  ];
  return $items;
}

/**
 * Update Topic Page nodes to use default topic type.
 */
function drush_mass_utility_mass_topic_page_type_update() {
  // Get all the topic nodes without a topic type.
  $query = \Drupal::entityQuery('node');
  $nids = $query->condition('type', 'topic_page')
    ->notExists('field_topic_type')
    ->execute();
  $nodes = Node::loadMultiple($nids);
  foreach ($nodes as $node) {
    // Save the node with the default topic type.
    $node->field_topic_type->setValue('topic');
    $node->save();
  }
}
