<?php

namespace Drupal\mass_serializer\Plugin\views\style;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\rest\Plugin\views\style\Serializer;

/**
 * The style plugin for serialized output formats using Project Open Data v1.1 format.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "mass_pod_serializer",
 *   title = @Translation("Mass POD v1.1 Data.json Serializer"),
 *   help = @Translation("Serializes views row data using the Serializer component."),
 *   display_types = {"data"}
 * )
 */
class MassPODSerializer extends Serializer implements CacheableDependencyInterface {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $rows = [];
    // If the Data Entity row plugin is used, this will be an array of entities
    // which will pass through Serializer to one of the registered Normalizers,
    // which will transform it to arrays/scalars. If the Data field row plugin
    // is used, $rows will not contain objects and will pass directly to the
    // Encoder.
    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $rows[] = $this->view->rowPlugin->render($row);
    }
    unset($this->view->row_index);

    // Get the content type configured in the display or fallback to the
    // default.
    if ((empty($this->view->live_preview))) {
      $content_type = $this->displayHandler->getContentType();
    }
    else {
      $content_type = !empty($this->options['formats']) ? reset($this->options['formats']) : 'json';
    }

    $rows = [
      '@context' => 'https://project-open-data.cio.gov/v1.1/schema/catalog.jsonld',
      '@id' => 'http://mass.gov/data.json',
      '@type' => 'dcat:Catalog',
      'conformsTo' => 'https://project-open-data.cio.gov/v1.1/schema',
      'describedBy' => 'https://project-open-data.cio.gov/v1.1/schema/catalog.json',
      'dataset' => $rows
    ];

    return $this->serializer->serialize($rows, $content_type, ['views_style_plugin' => $this]);
  }

}
