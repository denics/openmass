<?php

namespace Drupal\mass_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\mass_map\MapLocationFetcher;

/**
 * Class MapController.
 *
 * @package Drupal\mass_map\Controller
 */
class MapController extends ControllerBase {

  /**
   * Content for the map pages.
   *
   * @param int $id
   *   Subtopic nid with a list of locations.
   *
   * @return array
   *   Render array that returns a list of locations.
   */
  public function content($id) {

    $ids = $this->getMapLocationIds($id);

    $location_fetcher = new MapLocationFetcher();
    // Use the ids to get location info.
    $locations = $location_fetcher->getLocations($ids);

    return [
      '#theme' => 'map_page',
      '#locationListing' => $locations,
      '#attached' => [
        'library' => [
          'mass_map/mass-map-page-renderer',
          'mass_map/mass-google-map-apis',
        ],
        'drupalSettings' => [
          'locations' => $locations,
        ],
      ],
    ];
  }

  /**
   * Get a list of location ids from the parent id.
   *
   * @param int $id
   *   The nid that contains a map row paragraph or location nids.
   *
   * @return array
   *   A list of location node ids.
   */
  private function getMapLocationIds($id) {
    // Get Locations from the given subtopic.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    $node = $node_storage->load($id);

    $locationIds = [];

    $locationIds = $this->getLocationIds($node);

    // Extract location info from right rail layout.
    if ($node->getType() == 'action') {
      $locationIds = $this->getActionLocationIds($node);
    }
    // Extract location info from stacked layout.
    if ($node->getType() == 'stacked_layout') {
      $locationIds = $this->getStackedLayoutLocationIds($node);
    }
    return $locationIds;
  }

  /**
   * Get location ids from Right Rail node.
   *
   * @param object $node
   *   Right Rail node.
   *
   * @return array
   *   And array containing location ids.
   */
  private function getActionLocationIds($node) {
    $locationIds = [];

    // Get map row out of the details paragraph.
    if (!empty($node->field_action_details)) {
      foreach ($node->field_action_details as $detail_id) {
        $detail = Paragraph::load($detail_id->target_id);
        if ($detail->getType() == 'map_row') {
          foreach ($detail->field_map_locations as $location) {
            $locationIds[] = $location->target_id;
          }
          break;
        }
      }
    }
    return $locationIds;
  }

  /**
   * Get location ids from Stacked Layout node.
   *
   * @param object $node
   *   Stacked Layout node.
   *
   * @return array
   *   And array containing location ids.
   */
  private function getStackedLayoutLocationIds($node) {
    $locationIds = [];

    if (!empty($node->field_bands)) {
      foreach ($node->field_bands as $band_id) {
        // Search the main bands field a map row.
        $band = Paragraph::load($band_id->target_id);
        if (!empty($band->field_main)) {
          foreach ($band->field_main as $band_main_id) {
            $band_main = Paragraph::load($band_main_id->target_id);
            if ($band_main->getType() == 'map_row') {
              foreach ($band_main->field_map_locations as $location) {
                $locationIds[] = $location->target_id;
              }
              break;
            }
          }
        }
      }
    }
    return $locationIds;
  }

  /**
   * Get location ids from node.
   *
   * @param object $node
   *   Current node.
   *
   * @return array
   *   And array containing location ids.
   */
  private function getLocationIds($node) {
    $locationIds = [];

    if (!empty($node->field_org_ref_locations)) {
      foreach ($node->field_org_ref_locations as $entity) {
        $locationIds[] = $entity->target_id;
      }
    }
    return $locationIds;
  }

}
