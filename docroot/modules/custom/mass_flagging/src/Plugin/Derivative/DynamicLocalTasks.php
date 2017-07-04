<?php

/**
 * @file
 * Contains \Drupal\example\Plugin\Derivative\DynamicLocalTasks.
 */

namespace Drupal\mass_flagging\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic local tasks.
 */
class DynamicLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * Routematch.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface;
   */
  protected $routeMatch;

  /**
   * Constructs a \Drupal\mass_flagging\Plugin\Derivative\ViewsLocalTask instance.
   *
   * @param string $base_plugin_id
   * @param RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct($base_plugin_id, RouteMatchInterface $route_match) {
    $this->basePluginId = $base_plugin_id;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Implement dynamic logic to provide values for the same keys as in example.links.task.yml.
    $this->derivatives['mass_flagging.task_id'] = $base_plugin_definition;
    #TODO: get txt value from flag to replace static
    $this->derivatives['mass_flagging.task_id']['title'] = "Watch";

    $node = $this->routeMatch->getParameter('node');
    if ($node) {
      $node_id = $node->id();
    }
//    $this->derivatives['mass_flagging.task_id']['route_name'] = 'entity.node.delete_form';
    $this->derivatives['mass_flagging.task_id']['route_name'] = 'flag.action_link_flag';
    $this->derivatives['mass_flagging.task_id']['route_parameters'] = [
      'flag' => 'watch_content',
      'entity_id' => $node_id
    ];


    $this->derivatives['mass_flagging.task_id']['base_route'] = 'entity.node.canonical';
    return $this->derivatives;
  }

}