<?php

namespace Drupal\mass_content_api;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Database\Connection;

/**
 * Class DescendantManager.
 *
 * @package Drupal\mass_content_api
 */
class DescendantManager implements DescendantManagerInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Database\Connection definition.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new DescendantManager object.
   */
  public function __construct(EntityTypeManager $entity_type_manager, Connection $database_connection) {
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database_connection;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelationships($node_id) {
    $this->deleteRelationships($node_id);

    $node = $this->entityTypeManager->getStorage('node')
      ->load($node_id);

    if (empty($node)) {
      return;
    }

    $rel = $this->getRelationshipConfiguration();

    if (isset($rel[$node->bundle()]['parent'])) {
      $parent_field = $rel[$node->bundle()]['parent'];
      foreach ($node->{$parent_field} as $item) {
        $this->setRelationship($node->id(), $item->target_id, $node->id());
      }
    }
    if (isset($rel[$node->bundle()]['child'])) {
      foreach ($rel[$node->bundle()]['child'] as $child_rel) {
        $children = [];
        // Switch types for now.
        // @todo: Get relationship function from relationship plugin.
        switch ($child_rel['type']) {
          case 'entity_reference':
            foreach ($node->{$child_rel['field']} as $item) {
              $children[] = $item->target_id;
            }
            break;

          case 'field_on_paragraph':
            foreach ($node->{$child_rel['field']} as $item) {
              if (!empty($item->entity->{$child_rel['paragraph_field']}->target_id)) {
                $children[] = $item->entity->{$child_rel['paragraph_field']}->target_id;
              }
            }
            break;

          case 'link_field_on_paragraph_from_paragraph':
            foreach ($node->{$child_rel['field']} as $item) {
              foreach ($item->entity->{$child_rel['paragraph_field']} as $group) {
                if (!empty($group->uri)) {
                  $value = $group->uri;
                  if (strpos($value, 'entity:node') !== FALSE) {
                    $parts = explode('/', $value);
                    $children[] = $parts[1];
                  }
                }
              }
            }
            break;

          case 'link':
            foreach ($node->{$child_rel['field']} as $item) {
              if (!empty($item->uri)) {
                $value = $item->uri;
                if (strpos($value, 'entity:node') !== FALSE) {
                  $parts = explode('/', $value);
                  $children[] = $parts[1];
                }
              }
            }
            break;

        }
        foreach ($children as $child) {
          $this->setRelationship($node->id(), $node->id(), $child);
        }
      }
    }
  }

  /**
   * Delete items related to this node.
   *
   * @param string $reporter
   *   The id of the content storing the relationship field.
   */
  private function deleteRelationships($reporter) {
    // Delete any relationships previously defined by this item.
    $this->database->delete(DESCENDANT_TABLE)
      ->condition('reporter', $reporter)->execute();
  }

  /**
   * Set a parent child relationship.
   *
   * @param int $reporter
   *   The id of the content storing the relationship field.
   * @param int $parent
   *   The id of the parent.
   * @param int $child
   *   The id of the child.
   */
  private function setRelationship($reporter, $parent, $child) {
    // Add the relationship.
    $this->database->insert(DESCENDANT_TABLE)
      ->fields(['reporter', 'parent', 'child'])
      ->values([$reporter, $parent, $child])
      ->execute();
  }

  /**
   * Get information about content relationships.
   *
   * @return array
   *   The relationships defined between nodes.
   */
  public function getRelationshipConfiguration() {
    return [
      'advisory' => [
        'parent' => 'field_advisory_ref_organization',
      ],
      'decision' => [
        'parent' => 'field_decision_ref_organization',
      ],
      'event' => [
        'parent' => 'field_event_ref_parents',
      ],
      'executive_order' => [
        'parent' => 'field_executive_order_ref_org',
      ],
      'location' => [
        'child' => [
          [
            'type' => 'field_on_paragraph',
            'field' => 'field_location_activity_detail',
            'paragraph_field' => 'field_ref_location_details_page',
          ],
        ],
      ],
      'org_page' => [
        'parent' => 'field_ref_actions_6',
        'child' => [
          [
            'type' => 'entity_reference',
            'field' => 'field_org_ref_locations',
          ],
        ],
      ],
      'service_page' => [
        'child' => [
          [
            'type' => 'entity_reference',
            'field' => 'field_service_eligibility_info',
          ],
          [
            'type' => 'entity_reference',
            'field' => 'field_service_ref_locations',
          ],
          [
            'type' => 'entity_reference',
            'field' => 'field_service_ref_guide_page_1',
          ],
          [
            'type' => 'link',
            'field' => 'field_service_ref_actions',
          ],
          [
            'type' => 'link',
            'field' => 'field_service_ref_actions_2',
          ],
        ],
      ],
      'topic_page' => [
        'child' => [
          [
            'type' => 'link_field_on_paragraph_from_paragraph',
            'field' => 'field_topic_content_cards',
            'paragraph_field' => 'field_content_card_link_cards',
          ],
        ],
      ],
      'regulation' => [
        'parent' => 'field_regulation_ref_org',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren($node_id, $flat = FALSE, $limit = 20) {
    static $level = 0;
    $level++;
    $descendants = [];
    if ($level < $limit) {
      $query = $this->database->select(DESCENDANT_TABLE, 'dt')
        ->fields('dt', ['child'])
        ->condition('parent', $node_id);

      $results = $query->execute()->fetchCol();
      foreach ($results as $result) {
        if ($flat) {
          $descendants[] = $result;
          $descendants = array_merge($descendants, $this->getChildren($result, TRUE));
        }
        else {
          $descendants[] = [
            'id' => $result,
            'children' => $this->getChildren($result),
          ];
        }
      }
    }

    $level--;
    return $descendants;
  }

}
