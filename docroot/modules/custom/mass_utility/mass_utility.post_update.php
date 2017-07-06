<?php

/**
 * @file
 * Run updates after updatedb has completed.
 */

use Drupal\node\Entity\Node;

/**
 * Populate new address field.
 *
 * See https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Extension%21module.api.php/function/hook_post_update_NAME/8.3.x.
 */
function mass_utility_post_update_address_field() {
  $eq = \Drupal::entityQuery('node');
  $nids = $eq->condition('type', 'contact_information')->execute();
  /** @var Drupal\node\Entity\Node[] $nodes */
  $nodes = Node::loadMultiple($nids);
  foreach ($nodes as $node) {
    // @todo Parse local CSV file.
    $value = 'foo';
    // $node->field_address_address->addressLine1->setValue($value);
    // $node->save();
  }
}
