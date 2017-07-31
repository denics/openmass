<?php

namespace Drupal\mass_content_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a listing of all content node IDs w/ IDs of parent nodes.
 *
 * @RestResource(
 *   id = "content_metadata_resource",
 *   label = @Translation("Content Metadata Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/content-metadata"
 *   }
 * )
 */
class ContentMetadataResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * GET query params:
   *   limit - max number of results to return.
   *   offset - number of results to skip.
   *
   * @return \Drupal\rest\ResourceResponse
   *   API response containing metadata & results keys.
   */
  public function get() {

    // Transforms a Node item into a simple object,
    // containing an ID attribute, and parent IDs array.
    $extract_meta_from_nodes = function ($item) {
      // Refernces to various 'parent reference fields'.
      $get_target_id = function ($r) {
        return $r['target_id'];
      };

      $parent_ref_fields = [
        'field_action_parent',
        'field_topic_parent',
        'field_section',
      ];
      // Node owner/author data.
      $node_owner = $item->getOwner();
      $org = Term::load($node_owner->field_user_org->target_id);
      $roles = array_map($get_target_id, $node_owner->roles->getValue());
      $mod_states = array_map($get_target_id, $item->moderation_state->getValue());
      $res = [
        'parent_ids' => [],
        'id' => $item->nid->value,
        'uuid' => $item->uuid->value,
        'title' => $item->title->value,
        'date_created' => $item->created->value,
        'date_changed' => $item->changed->value,
        'content_type' => $item->getType(),
        'published' => $item->status->value == 1,
        'promoted' => $item->promote->value == 1,
        'sticky' => $item->sticky->value == 1,
        'moderation_state' => $mod_states,
        'author' => [
          'id' => $node_owner->uid->value,
          'uuid' => $node_owner->uuid->value,
          'name' => $node_owner->name->value,
          'organization' => [
            'name' => $org->name->value,
            'uuid' => $org->uuid->value,
            'id' => $org->tid->value
          ],
          'is_intern' => $node_owner->field_user_intern->value == 1,
          'roles' => $roles,
        ]
      ];

      foreach ($parent_ref_fields as $p_field) {
        if (isset($item->{$p_field})) {
          foreach ($item->{$p_field} as $parent) {
            $res['parent_ids'][] = $item->{$p_field}->target_id;
          }
        }
      }
      return $res;
    };

    // Set limit & offset query params.
    $query_params = \Drupal::request()->query->all();
    $offset_num = 0;
    $record_limit = 1000;
    if (isset($query_params['offset'])) {
      $offset_num = (int) $query_params['offset'];
    }
    if (isset($query_params['limit'])) {
      $record_limit = min($record_limit, (int) $query_params['limit']);
    }

    $query = \Drupal::entityQuery('node');
    $query->range($offset_num, $record_limit);
    $entity_ids = $query->execute();

    $node_storage = \Drupal::entityManager()->getStorage('node');
    $nodes = $node_storage->loadMultiple($entity_ids);
    $results = [];

    foreach ($nodes as $n) {
      $results[] = $extract_meta_from_nodes($n);
    }

    $output = [
      'data' => $results,
      'metadata' => [
        'resultset' => [
          'count' => count($results),
          'limit' => $record_limit,
          'offset' => $offset_num,
        ],
      ],
    ];

    // No caching.
    $cache_opts = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return (new ResourceResponse($output))->addCacheableDependency($cache_opts);
  }

}
