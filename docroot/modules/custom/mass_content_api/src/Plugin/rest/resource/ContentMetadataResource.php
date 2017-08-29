<?php

namespace Drupal\mass_content_api\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\mass_content_api\DescendantManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Path\AliasManagerInterface;

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
class ContentMetadataResource extends ResourceBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\mass_content_api\DescendantManager definition.
   *
   * @var \Drupal\mass_content_api\DescendantManagerInterface
   */
  protected $descendantManager;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * Alias Manager definition.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  private $aliasManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, DescendantManagerInterface $descendant_manager, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, AliasManagerInterface $alias_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->descendantManager = $descendant_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('descendant_manager'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
      $container->get('path.alias_manager')
    );
  }

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
    $extract_meta_from_nodes = function ($item, $format) {
      // References to various 'parent reference fields'.
      $get_target_id = function ($r) {
        return $r['target_id'];
      };

      // Node owner/author data.
      $node_owner = $item->getOwner();
      $org = Term::load($node_owner->field_user_org->target_id);
      $roles = array_map($get_target_id, $node_owner->roles->getValue());
      $mod_states = array_map($get_target_id, $item->moderation_state->getValue());
      $path = $this->aliasManager->getAliasByPath('/node/' . $item->nid->value);
      $res = [
        'descendants' => [],
        'id' => $item->nid->value,
        'node_path' => $path,
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
            'id' => $org->tid->value,
          ],
          'is_intern' => $node_owner->field_user_intern->value == 1,
          'roles' => $roles,
        ],
      ];

      $res['descendants'] = $this->descendantManager->getChildren($item->nid->value, $format);

      return $res;
    };

    // Set query params.
    $query_params = $this->requestStack->getCurrentRequest()->query->all();
    $offset_num = 0;
    $record_limit = 1000;
    $descendant_format = TRUE;
    if (isset($query_params['offset'])) {
      $offset_num = (int) $query_params['offset'];
    }
    if (isset($query_params['limit'])) {
      $record_limit = min($record_limit, (int) $query_params['limit']);
    }
    if (isset($query_params['descendant_format'])) {
      $descendant_format = ($query_params['descendant_format'] != MASS_CONTENT_API_DEPTH);
    }
    if (isset($query_params['content_types'])) {
      $content_types = explode(',', $query_params['content_types']);
    }
    if (isset($query_params['published'])) {
      $published = 1;
    }

    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery();
    if (isset($published)) {
      $query->condition('status', $published);
    }
    if (!empty($content_types)) {
      $query->condition('type', $content_types, 'IN');
    }
    $query->range($offset_num, $record_limit);
    $entity_ids = $query->execute();

    $nodes = $node_storage->loadMultiple($entity_ids);
    $results = [];

    foreach ($nodes as $n) {
      $results[] = $extract_meta_from_nodes($n, $descendant_format);
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
