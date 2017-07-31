<?php

namespace Drupal\mass_schema_apply_action\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_apply_action_location' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_apply_action_location",
 *   label = @Translation("location"),
 *   description = @Translation("A location page of the item."),
 *   name = "location",
 *   group = "schema_apply_action",
 *   weight = 1,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaApplyActionLocation extends SchemaNameBase {

  /**
   * Generate a form element for this meta tag.
   */
  public function form(array $element = []) {
    $form = parent::form($element);
    $form['#attributes']['placeholder'] = '[node:field_how_to_contacts_3]';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function output() {
    $element = parent::output();
    // Decode the value because it was encoded by hook_tokens().
    $addresses = json_decode($this->value(), TRUE);

    $element['#attributes']['content'] = [];

    // Iterate through aech address and add it to the array output.
    foreach ($addresses as $address) {
      // Compute the streetAddress value by combining line1 and line2.
      $address_line = $address['address_line1'];
      if (!empty($address['address_line2'])) {
        $address_line .= ' ' . $address['address_line2'];
      }
      $element['#attributes']['content'][] = [
        '@type' => 'PostalAddress',
        'addressCountry' => $address['country_code'],
        'addressLocality' => $address['locality'],
        'addressRegion' => $address['administrative_area'],
        'postalCode' => $address['postal_code'],
        'streetAddress' => $address_line,
      ];
    }

    return $element;
  }

}
