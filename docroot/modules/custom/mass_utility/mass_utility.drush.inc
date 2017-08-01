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
  $items['mass-audience-field-update'] = [
    'description' => 'Add the default value to nodes with an Audience field',
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

/**
 * Add the default value to nodes with an Audience field.
 */
function drush_mass_utility_mass_audience_field_update() {
  // Get all nodes of the appropriate types where field_audience is not set.
  $types = ['guide_page', 'how_to_page', 'service_details', 'service_page'];
  $nids = \Drupal::entityQuery('node')
    ->condition('type', $types, 'IN')
    ->notExists('field_audience')
    ->execute();

  // Load 50 nodes at a time and give them a default audience field value.
  $offset = 0;
  $limit = 50;
  while (!empty($nodes = Node::loadMultiple(array_slice($nids, $offset, $limit)))) {
    foreach ($nodes as $node) {
      $node->field_audience->setValue('constituents');
      $node->save();
    }
    $offset += $limit;
  }
}