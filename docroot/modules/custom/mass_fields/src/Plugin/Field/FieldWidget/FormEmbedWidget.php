<?php

namespace Drupal\mass_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use DOMDocument;
use DOMXPath;

/**
 * A form embed widget.
 *
 * @FieldWidget(
 *   id = "form_embed",
 *   label = @Translation("Form Embed widget"),
 *   field_types = {
 *     "form_embed"
 *   }
 * )
 */
class FormEmbedWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $type = isset($items[$delta]->type) ? $items[$delta]->type : '';
    $element['value'] = [
      '#title' => 'Embedded form',
      '#type' => 'textarea',
      '#default_value' => $value,
      '#description' => t('After you have built your form in FormStack, paste the embed code here.'),
      '#rows' => 5,
    ];

    $element['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Form Embed Type'),
      '#default_value' => $type,
      '#options' => [
        'formstack' => 'Formstack',
      ],
      '#required' => TRUE,
    ];
    $element['#element_validate'] = [[get_called_class(), 'validate']];

    return ['value' => $element];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $delta => $value) {
      $new_values[$delta]['value'] = $value['value']['value'];
      $new_values[$delta]['type'] = $value['value']['type'];
    }
    return $new_values;
  }

  /**
   * Validate embed text.
   */
  public function validate(&$element, FormStateInterface $form_state) {
    $value = $element['value']['#value'];
    $type = $element['type']['#value'];

    // Validate empty field.
    if (strlen($value) == 0) {
      $form_state->setValueForElement($element, '');
      return;
    }

    // Check formstack embed code for url and id.
    if ($type == 'formstack') {
      $dom = new DOMDocument();
      @$dom->loadHTML($value);
      $xpath = new DOMXPath($dom);

      // Validate we have a formstack url.
      $url = $xpath->evaluate('string(//noscript/a/@href)');
      if (!$url) {
        $form_state->setError($element['value'], t("Malformed embed code. FormStack embed must contain a formtack URL."));
      }

      // Validate we have a formstack ID.
      $id_url = $xpath->evaluate('string(//div/a/@href)');
      if ($id_url) {
        parse_str($id_url, $id_url_arr);
        if (array_key_exists('fa', $id_url_arr)) {
          $id = preg_replace('~\D~', '', $id_url_arr['fa']);
          if (!empty($id)) {
            return;
          }
        }
      }
      $form_state->setError($element['value'], t("Malformed embed code. FormStack embed must contain a formtack ID."));
    }
  }

}
