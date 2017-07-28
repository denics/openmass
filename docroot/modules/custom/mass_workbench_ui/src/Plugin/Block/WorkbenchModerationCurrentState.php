<?php

namespace Drupal\mass_workbench_ui\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\workbench_moderation\ModerationInformation;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a Block that displays the revision state.
 *
 * @Block(
 *   id = "current_revision",
 *   admin_label = @Translation("Current revision state"),
 * )
 */
class WorkbenchModerationCurrentState extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $nid = \Drupal::routeMatch()->getRawParameter('node');

    // Cache the block based on node id and url.
    $block = [
      '#cache' => [
        'contexts' => ['url.path'],
        'tags' => ['node:' . $nid],
      ],
    ];

    // Get the state for the latest revision.
    if (\Drupal::routeMatch()->getRouteName() == 'entity.node.latest_version') {
      $moderation_info = new ModerationInformation(\Drupal::entityTypeManager(), \Drupal::currentUser());
      $node = $moderation_info->getLatestRevision('node', $nid);
      $block['#markup'] = "Current moderation state: " . $node->moderation_state->target_id;
      return $block;
    }

    // Get the state for revisions.
    if (\Drupal::routeMatch()->getRouteName() == 'entity.node.revision') {
      $revision_id = $nid = \Drupal::routeMatch()->getRawParameter('node_revision');
      $node = node_revision_load($revision_id);
      $block['#markup'] = "Current moderation state: " . $node->moderation_state->target_id;
      return $block;
    }

    // Get the state of the default revision.
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    $block['#markup'] = "Current moderation state: " . $node->moderation_state->target_id;

    return $block;
  }

  /**
   * {@inheritdoc}
   *
   * Only show this block on node pages to users who have correct permission.
   */
  protected function blockAccess(AccountInterface $account) {
    $route_name = \Drupal::routeMatch()->getRouteName();

    // Dont show if user does not have the permission.
    if (!$account->hasPermission('view current moderation states')) {
      return AccessResult::forbidden();
    }

    $routes = [
      'entity.node.canonical',
      'entity.node.latest_version',
      'entity.node.revision',
    ];

    // Only show on node pages.
    if (in_array($route_name, $routes)) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

}
