<?php

/**
 * @file
 * Contains \Drupal\example\Plugin\Derivative\DynamicLocalTasks.
 */

namespace Drupal\mass_flagging\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Defines dynamic local tasks.
 */
class DynamicLocalTasks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Implement dynamic logic to provide values for the same keys as in example.links.task.yml.
    $this->derivatives['mass_flagging.task_id'] = $base_plugin_definition;
    $this->derivatives['mass_flagging.task_id']['title'] = "Watching";
//    $this->derivatives['mass_flagging.task_id']['route_name'] = 'entity.node.delete_form';
    $this->derivatives['mass_flagging.task_id']['route_name'] = 'flag.action_link_flag';
    $this->derivatives['mass_flagging.task_id']['base_route'] = 'entity.node.canonical';
    return $this->derivatives;
  }

}