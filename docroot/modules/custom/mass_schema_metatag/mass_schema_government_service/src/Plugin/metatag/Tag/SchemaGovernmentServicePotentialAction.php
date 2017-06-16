<?php

namespace Drupal\mass_schema_government_service\Plugin\metatag\Tag;

use Drupal\metatag\Plugin\metatag\Tag\LinkRelBase;
use \Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_government_service_potential_action' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_government_service_potential_action",
 *   label = @Translation("Potential Action"),
 *   description = @Translation("The potential action of the item."),
 *   name = "potentialAction",
 *   group = "schema_government_service",
 *   weight = 1,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = TRUE
 * )
 */
class SchemaGovernmentServicePotentialAction extends SchemaNameBase {

  /**
   * Generate a form element for this meta tag.
   */
  public function form(array $element = []) {
    $form = parent::form($element);
    $form['#attributes']['placeholder'] = '[node:title]';
    return $form;
  }

  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function output() {
    $element = parent::output();

    $content = explode(', ', $this->value());

    $element['#attributes']['content'] = [];

    foreach ($content as $link_values) {
      $link_values = json_decode($link_values, true);
      if (is_array($link_values)) {
        foreach ($link_values as $item) {
          $element['#attributes']['content'][] = [
            'name' => $item['name'],
            'url' => $item['url'],
          ];
        }
      }
      else {
        $element['#attributes']['content'][] = [
          'name' => $link_values['name'],
          'url' => $link_values['url'],
        ];
      }
    }

    return $element;
  }

}
