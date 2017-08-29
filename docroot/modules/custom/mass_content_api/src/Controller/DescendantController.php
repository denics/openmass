<?php

namespace Drupal\mass_content_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\mass_content_api\DescendantManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DescendantController.
 *
 * @package Drupal\mass_content_api\Controller
 */
class DescendantController extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeBundleInfo definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $bundleInfo;

  /**
   * Drupal\mass_content_api\DescendantManagerInterface definition.
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
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeBundleInfoInterface $bundle_info, DescendantManagerInterface $descendant_manager, EntityTypeManager $entity_type_manager, RequestStack $request_stack) {
    $this->bundleInfo = $bundle_info;
    $this->descendantManager = $descendant_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityQuery = $entity_type_manager->getStorage('node')->getQuery();
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.bundle.info'),
      $container->get('descendant_manager'),
      $container->get('entity_type.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * Build a display of content descendants.
   *
   * GET parameters can be used to specify the content to return.
   *   - id: specify to show descendants of this node specifically.
   *   - content_type: specify to show descendants of this type.
   *   - format: specify 'depth' if you would like to see multiple levels of
   *     relationships instead of all in just one list.
   *   Examples:
   *     - ?id=1234
   *     - ?content_type=service_page&format=depth
   * If id and content_type are both specified, only content_type will be used.
   *
   * @return array
   *   The render array to display on the page.
   */
  public function build() {
    $output = '<h2>Parameters help</h2>';
    $output .= 'Add parameters to the url to show filtered or adjust output.';
    $output .= '<ul><li>Use <em>?id=[node id]</em> to see descendants for a specific ID.';
    $output .= '<li>Use <em>?content_type=[content_type]</em> to see descendants for a specific Content Type.</li>';
    $output .= '<li>Add <em>&format=depth</em> to retain the depth structure in the output.</li></ul>';
    $output .= '<h3>Examples</h3>';
    $output .= '<ul><li>/admin/config/content/descendants?id=1234</li>';
    $output .= '<li>/admin/config/content/descendants?content_type=service_page</li>';
    $output .= '<li>/admin/config/content/descendants?content_type=service_page&format=depth</li></ul>';
    $query_params = $this->requestStack->getCurrentRequest()->query->all();
    if (!empty($query_params['id']) || !empty($query_params['content_type'])) {
      $format = MASS_CONTENT_API_FLAT;
      if (!empty($query_params['format'])) {
        $format = ($query_params['format'] != MASS_CONTENT_API_DEPTH);
      }
      if (!empty($query_params['content_type'])) {
        $bundles = $this->bundleInfo->getBundleInfo('node');
        if (in_array($query_params['content_type'], array_keys($bundles))) {
          // Load and print descendant tree for all nodes of this bundle.
          $query = $this->entityQuery
            ->condition('type', $query_params['content_type'])
            ->condition('status', 1)
            ->pager(20);
          $results = $query->execute();
          foreach ($results as $result) {
            $node = $this->entityTypeManager->getStorage('node')->load($result);
            if (!empty($node)) {
              $output .= $this->printDescendants($node, $format);
            }
          }
        }
      }
      elseif (!empty($query_params['id'])) {
        // Load and print descendant tree for just this node.
        $node = $this->entityTypeManager->getStorage('node')
          ->load($query_params['id']);
        if (!empty($node)) {
          $output .= $this->printDescendants($node, $format);
        }
      }
    }

    return [
      'descendants' => [
        '#type' => 'markup',
        '#markup' => $output,
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  /**
   * Print the descendants of a specific node.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node object to use for printing.
   * @param int $flat
   *   The format, either flat(1) or with depth(2).
   *
   * @return string
   *   The markup for the descendants.
   */
  private function printDescendants(Node $node, $flat) {
    $output = $this->printParent($node);

    $children = $this->descendantManager->getChildren($node->id(), $flat);
    if ($flat) {
      $output .= $this->printChildren($children);
    }
    else {
      $output .= $this->printChildrenDepth($children);
    }
    return $output;
  }

  /**
   * Print a parent in the descendant output.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node object to use to print the parent information.
   *
   * @return string
   *   The markup output of the parent.
   */
  private function printParent(Node $node) {
    $output = '<h2><a href="' . $node->toUrl()->toString() . '">';
    $output .= $node->label() . '</a>';
    $output .= ' (' . $node->id() . ' | ' . $node->bundle() . ' | ';
    $output .= '<a href="' . $node->toUrl('edit-form')->toString() . '">edit</a> ).</h2>';

    return $output;
  }

  /**
   * Print the children in the descendant output.
   *
   * @param array $children
   *   A list of ids identified as children.
   *
   * @return string
   *   The flat list of the children.
   */
  private function printChildren(array $children) {
    $output = '<ul>';
    foreach ($children as $child) {
      $child_node = $this->entityTypeManager->getStorage('node')->load($child);
      if (!empty($child_node)) {
        $output .= '<li><a href="' . $child_node->toUrl()->toString() . '">';
        $output .= $child_node->label() . '</a>';
        $output .= ' (' . $child_node->id() . ' | ' . $child_node->bundle();
        $output .= ' | <a href="';
        $output .= $child_node->toUrl('edit-form')->toString();
        $output .= '">edit</a> ).</li>';
      }
    }
    $output .= '</ul>';

    return $output;
  }

  /**
   * Print the children in the descendant output.
   *
   * @param array $children
   *   A list of ids identified as children.
   *
   * @return string
   *   The list of the children with depth.
   */
  private function printChildrenDepth(array $children) {
    $output = '<ul>';
    foreach ($children as $child) {
      $child_node = $this->entityTypeManager->getStorage('node')->load($child['id']);
      if (!empty($child_node)) {
        $output .= '<li><a href="' . $child_node->toUrl()->toString() . '">';
        $output .= $child_node->label() . '</a>';
        $output .= ' (' . $child_node->id() . ' | ' . $child_node->bundle();
        $output .= ' | <a href="';
        $output .= $child_node->toUrl('edit-form')->toString();
        $output .= '">edit</a> ).';
        $output .= $this->printChildrenDepth($child['children']);
        $output .= '</li>';
      }
    }
    $output .= '</ul>';

    return $output;
  }

}
