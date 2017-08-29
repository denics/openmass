<?php

namespace Drupal\mass_content_api;

/**
 * Interface DescendantManagerInterface.
 *
 * @package Drupal\mass_content_api
 */
interface DescendantManagerInterface {

  /**
   * Sets relationships based on a node.
   *
   * @param int $node_id
   *   The node id of the node to be used to discover relationships.
   */
  public function setRelationships($node_id);

  /**
   * Return a configuration to define the relationships.
   *
   * @return array
   *   The representation of the node relationships to process.
   */
  public function getRelationshipConfiguration();

  /**
   * Gets all children of a node.
   *
   * @param int $node_id
   *   The node id of the node to be used to discover relationships.
   * @param bool $flat
   *   Whether to return in a flat array or hierarchy.
   * @param int $limit
   *   The number of levels this function is allowed to recurse.
   *
   * @return array
   *   A list of all ids of child content of the node.
   */
  public function getChildren($node_id, $flat = FALSE, $limit = 20);

}
