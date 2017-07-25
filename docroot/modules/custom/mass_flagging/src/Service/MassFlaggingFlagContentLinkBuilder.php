<?php

namespace Drupal\mass_flagging\Service;

use Drupal\Core\Url;

/**
 * Class MassFlaggingFlagContentLinkBuilder.
 *
 * @package Drupal\mass_flagging\Service
 */
class MassFlaggingFlagContentLinkBuilder {

  /**
   * Default tile of link to contact form for flagging content.
   *
   * @var string
   */
  protected $defaultLinkTitle;

  /**
   * Default contact form ID for flagging content.
   *
   * @var string
   */
  protected $defaultFormId;

  /**
   * Default reference field ID from contact form.
   *
   * @var string
   */
  protected $defaultFieldId;

  /**
   * Constructs a new MassFlaggingFlagContentLinkBuilder object.
   *
   * @param string $default_link_title
   *   Default link title.
   * @param string $default_form_id
   *   Default contact form ID.
   * @param string $default_field_id
   *   Default reference field machine name within contact form.
   */
  public function __construct($default_link_title, $default_form_id, $default_field_id) {
    $this->defaultLinkTitle = $default_link_title;
    $this->defaultFormId = $default_form_id;
    $this->defaultFieldId = $default_field_id;
  }

  /**
   * Builds a link to the contact form for flagging content.
   *
   * Note: If providing a custom form or field, then both the $contact_form_id
   * and $reference_field_id params must be specified.
   *
   * @param int $id
   *   Entity ID.
   * @param string $link_title
   *   Title to be displayed as the link.
   * @param string $contact_form_id
   *   Contact form ID.
   * @param string $reference_field_id
   *   Reference field ID.
   *
   * @return array
   *   Render array containing link to the contact form for flagging content.
   */
  public function build($id = NULL, $link_title = NULL, $contact_form_id = NULL, $reference_field_id = NULL) {
    $link = [];

    if (!empty($id)) {
      $link['#type'] = 'link';

      // Set link title.
      $link['#title'] = empty($link_title) ? $this->defaultLinkTitle : $link_title;

      // Check for custom $contact_form_id and $reference_field_id params.
      if (empty($contact_form_id) || empty($reference_field_id)) {
        $contact_form_id = $this->defaultFormId;
        $reference_field_id = $this->defaultFieldId;
      }

      // Generate URL object for contact form for flagging content.
      $contact_form_url = Url::fromRoute('entity.contact_form.canonical', ['contact_form' => $contact_form_id], [
        // Set 'query' option for use by Prepopulate contrib module.
        // Will be used to pre-fill entity reference field in contact form.
        'query' => [
          'edit[' . $reference_field_id . ']' => $id,
        ],
        // Set 'attributes' option for URL.
        'attributes' => [
          'title' => t('Flag a piece of content that appears inappropriate or incorrect, and provide your reason for flagging it.'),
        ],
      ]);
      $link['#url'] = $contact_form_url;
    }

    return $link;
  }

}
