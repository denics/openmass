<?php

namespace Drupal\mass_schema_government_service\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for 'schema_government_service_potential_action' meta tag.
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
    $value = $this->unserialize($this->value());

    $form['#type'] = 'details';
    $form['#title'] = $this->label();
    $form['#description'] = $this->description();
    $form['#tree'] = TRUE;
    $form['#open'] = !empty($value['potentialAction']);
    $form['@type'] = [
      '#type' => 'select',
      '#title' => $this->t('@type'),
      '#default_value' => !empty($value['@type']) ? $value['@type'] : '',
      '#empty_option' => t('- None -'),
      '#empty_value' => '',
      '#options' => [
        'Action' => $this->t("Action"),
        'AchieveAction' => $this->t("AchieveAction"),
        'ConsumeAction' => $this->t("ConsumeAction"),
        'ControlAction' => $this->t("ControlAction"),
        'CreateAction' => $this->t("CreateAction"),
        'FindAction' => $this->t("FindAction"),
        'InteractAction' => $this->t("InteractAction"),
        'MoveAction' => $this->t("MoveAction"),
        'OrganizeAction' => $this->t("OrganizeAction"),
        'PlayAction' => $this->t("PlayAction"),
        'SearchAction' => $this->t("SearchAction"),
        'TradeAction' => $this->t("TradeAction"),
        'TransferAction' => $this->t("TransferAction"),
        'UpdateAction' => $this->t("UpdateAction"),
      ],
      '#required' => isset($element['#required']) ? $element['#required'] : FALSE,
    ];

    $form['potentialAction'] = [
      '#type' => 'textfield',
      '#title' => $this->t('potentialAction'),
      '#default_value' => !empty($value['potentialAction']) ? $value['potentialAction'] : '',
      '#maxlength' => 255,
      '#required' => isset($element['#required']) ? $element['#required'] : FALSE,
      '#description' => $this->t("The street address. For example, 1600 Amphitheatre Pkwy."),
      '#attributes' => [
        'placeholder' => '[node:title]',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function output() {
    $element = parent::output();

    $values = $this->unserialize($this->value());

    // Since there could be multiple values, explode the string value.
    $actions = explode(', ', $values['potentialAction']);

    $content = [];
    foreach ($actions as $action) {
      $decoded_value = json_decode($action, TRUE);
      if (is_array($decoded_value)) {
        foreach ($decoded_value as $item) {
          $content[] = $item;
        }
      }
      else {
        $content[] = $action;
      }
    }

    $element['#attributes']['content'] = [];

    foreach ($content as $link_values) {
      // Decode the link values.
      if (is_array($link_values)) {
        // For each link item, append the values of the 'name' and 'url' to the
        // 'content' key. This will be the value outputted on the markup.
        $element['#attributes']['content'][] = [
          '@type' => $values['@type'],
          'name' => $link_values['name'],
          'url' => $link_values['url'],
        ];
      }
      else {
        $element['#attributes']['content'][] = [
          '@type' => $values['@type'],
          'name' => $link_values['name'],
          'url' => $link_values['url'],
        ];
      }
    }

    return $element;
  }

}
