<?php

namespace Drupal\mayflower;

use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;
use Drupal\image\Entity\ImageStyle;
use Drupal\mayflower\Prepare\Molecules;

/**
 * Provides mayflower prepare functions with helper functions.
 */
class Helper {

  /**
   * Helper function to determine whether or not a field is populated.
   *
   * @param object $entity
   *   Entity that contains the field to be checked.
   * @param string $field_name
   *   The name of the field to be checked.
   *
   * @return bool
   *   Whether or not a field is populated.
   */
  public static function isFieldPopulated($entity, $field_name) {
    if (!method_exists($entity, 'hasField')) {
      return FALSE;
    }

    $is_populated = FALSE;

    $has_field = $entity->hasField($field_name);

    if ($has_field) {
      $field = $entity->get($field_name);
      if ($field->count() > 0) {
        $is_populated = TRUE;
      }
    }

    return $is_populated;
  }

  /**
   * Helper function to retrieve the fields needed by the pattern.
   *
   * @param object $entity
   *   The entity which contains the fields.
   * @param array $map
   *   The array which contains all potentially used fields.
   *
   * @return array
   *   The array which contains the fields used by this pattern.
   */
  public static function getMappedFields($entity, array $map) {
    $fields = [];
    // Determines which field names to use from the map.
    // @todo refactor to make use array functions (map, filter, reduce)
    foreach ($map as $id => $key) {
      foreach ($key as $field) {
        if ($entity->hasField($field)) {
          $fields[$id] = $field;
        }
      }
    }

    return $fields;
  }

  /**
   * Provide the URL of an image.
   *
   * @param object $entity
   *   The node with the field on it.
   * @param string $style_name
   *   (Optional) The name of an image style.
   * @param string $field
   *   The name of an the image field.
   * @param int $delta
   *   (Optional) the delta of the image field to display, defaults to 0.
   *
   * @return string
   *   The URL to the styled image, or to the original image if the style
   *   does not exist.
   */
  public static function getFieldImageUrl($entity, $style_name = NULL, $field = NULL, $delta = 0) {
    $url = '';

    $fields = $entity->get($field);

    if ($fields) {
      $images = $fields->referencedEntities();
    }

    if (!empty($images)) {
      $image = $images[$delta];

      if (!empty($style_name) && ($style = ImageStyle::load($style_name))) {
        $url = $style->buildUrl($image->getFileUri());
      }
      else {
        $url = $image->url();
      }
    }

    return $url;
  }

  /**
   * Helper function to provide url of an entity based on presence of a field.
   *
   * @param object $entity
   *   Entity object that contains the external url field.
   * @param string $external_url_link_field
   *   The name of the field.
   *
   * @return array
   *   Array that contains url and type (external, internal).
   */
  public static function getEntityUrl($entity, $external_url_link_field = '') {
    if ((!empty($external_url_link_field)) && (Helper::isFieldPopulated($entity, $external_url_link_field))) {
      // External URL field exists & is populated so get its URL + type.
      $links = Helper::separatedLinks($entity, $external_url_link_field);
      // @todo update mayflower_separated_links so we don't need [0]
      return $links[0];
    }
    else {
      // External URL field is non-existent or empty, get Node path alias.
      $url = $entity->toURL();
      return [
        'href' => $url->toString(),
        'type' => 'internal',
      ];
    }
  }

  /**
   * Helper function to provide separated link parts for multiple links.
   *
   * @param object $entity
   *   Entity object that contains the link field.
   * @param string $field
   *   The name of the field.
   * @param array $options
   *   An array of options.
   *
   * @return array
   *   Array that contains title, url and type (external, internal).
   */
  public static function separatedLinks($entity, $field, array $options = []) {
    $items = [];

    // Check if the target field is entity reference, else assume link field.
    if (Helper::isEntityReferenceField($entity, $field)) {
      // Retrieves the entities referenced from the entity field.
      $entities = Helper::getReferencedEntitiesFromField($entity, $field);

      foreach ($entities as $entity) {
        $text = $entity->get('title')->value;
        // Use date as title if specified.
        if (!empty($options['useDate']) && !empty($entity->$options['useDate']['fieldDate'])) {
          $date = \Drupal::service('date.formatter')->format(strtotime($entity->$options['useDate']['fieldDate']->value), 'custom', 'l, F d, Y');
          $text = $date;
          if (!empty($options['useDate']) && !empty($entity->$options['useDate']['fieldTime'])) {
            $time = $entity->$options['useDate']['fieldTime']->value;
            $text = $date . ', ' . $time;
          }
        }
        $items[] = [
          'url' => $entity->toURL()->toString(),
          'href' => $entity->toURL()->toString(),
          'text' => $text,
        ];
      }
    }
    else {
      $links = $entity->get($field);

      foreach ($links as $link) {
        $items[] = Helper::separatedLink($link, $options);
      }
    }

    return $items;
  }

  /**
   * Helper function to provide separated link parts.
   *
   * @param object $link
   *   The link object.
   * @param array $options
   *   An array of options.
   *
   * @return array
   *   Array that contains title, url and type (external, internal).
   */
  public static function separatedLink($link, array $options = []) {
    $url = $link->getUrl();
    $text = $link->getValue()['title'];
    $date = '';

    // On an internal item, load the referenced node title.
    if (strpos($link->getValue()['uri'], 'entity:node') !== FALSE) {
      $params = Url::fromUri("internal:" . $url->toString())->getRouteParameters();
      $entity_type = key($params);
      $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
      $content_type = $entity->getType();
      $content_type_name = $entity->type->entity->label();

      if (Helper::isFieldPopulated($entity, 'field_press_release_date')) {
        $date = Helper::fieldFullView($entity, 'field_press_release_date');
      }

      // On internal item, if empty, grab enity title.
      if (empty($text)) {
        $text = $entity->getTitle();
      }
    }

    $eyebrow = '';
    if (!empty($content_type) && isset($options['useEyebrow']) && in_array($content_type, $options['useEyebrow'])) {
      $eyebrow = $content_type_name;
    }

    return [
      'image' => '',
      'text' => $text,
      'type' => (UrlHelper::isExternal($url->toString())) ? 'external' : 'internal',
      'href' => $url->toString(),
      'url' => $url->toString(),
      'label' => '',
      'eyebrow' => $eyebrow,
      'date' => !empty($date) ? $date : '',
    ];
  }

  /**
   * Helper function to provide separated email link parts.
   *
   * @param object $entity
   *   Entity object that contains the link field.
   * @param string $field_name
   *   The name of the field.
   *
   * @return array
   *   Array that contains title, url.
   */
  public static function separatedEmailLink($entity, $field_name) {
    $link = $entity->get($field_name);

    return [
      'text' => $link->value,
      'href' => $link->value,
    ];
  }

  /**
   * Helper function to provide render array for a field.
   *
   * @param object $entity
   *   Entity that contains the field to render.
   * @param string $field_name
   *   The name of the field.
   *
   * @return array
   *   Returns the full render array of the field.
   */
  public static function fieldFullView($entity, $field_name) {
    $field_array = [];
    $field = $entity->get($field_name);

    if ($field->count() > 0) {
      $field_array = $field->first()->view('full');
    }

    return $field_array;
  }

  /**
   * Helper function to get the value of a referenced field.
   *
   * @param object $field
   *   Send a field object.
   * @param string $referenced_field
   *   Name of the referenced field.
   *
   * @return array
   *   The value of the referenced field.
   */
  public static function getReferenceField($field, $referenced_field) {
    if (method_exists($field, 'referencedEntities') && isset($field->referencedEntities()[0]) && $field->referencedEntities()[0]->hasField($referenced_field)) {
      return $field->referencedEntities()[0]->get($referenced_field)->value;
    }
    return FALSE;
  }

  /**
   * Helper function to retrieve the entities referenced from the entity field.
   *
   * @param object $entity
   *   The entity which contains the reference field.
   * @param string $reference_field
   *   The name of the entity reference field.
   *
   * @return array
   *   The array which contains the entities referenced by the field.
   */
  public static function getReferencedEntitiesFromField($entity, $reference_field) {
    // Retrieves the featured actions referenced from the entity field.
    $field = $entity->get($reference_field);
    $referenced_items = [];
    if ($field->count() > 0) {
      $referenced_items = $field->referencedEntities();
    }

    return $referenced_items;
  }

  /**
   * Helper function to provide a value for a field.
   *
   * @param object $entity
   *   Entity that contains the field to render.
   * @param string $field_name
   *   The name of the field.
   *
   * @return array
   *   Returns the value of the field.
   */
  public static function fieldValue($entity, $field_name) {
    $value = '';
    $field = $entity->get($field_name);
    if ($field->count() > 0) {
      $value = $field->first()->value;
    }
    return $value;
  }

  /**
   * Helper function to find the field names to use on the entity.
   *
   * @param array $referenced_entities
   *   Array that contains the featured/all actions referenced entities.
   * @param array $referenced_fields_map
   *   The array which contains the list of possible fields from the
   *   referenced entities.
   *
   * @return array
   *   The array which contains the list of necessary fields from the
   *   referenced entities.
   */
  public static function getMappedReferenceFields(array $referenced_entities, array $referenced_fields_map) {
    // @todo determine if this can be combined with mayflower_get_mapped_fields
    $referenced_fields = [];
    // Determines the field names to use on the referenced entity.
    foreach ($referenced_fields_map as $id => $key) {
      foreach ($key as $field) {
        if (isset($referenced_entities[0]) && $referenced_entities[0]->hasField($field)) {
          $referenced_fields[$id] = $field;
        }
      }
    }

    return $referenced_fields;
  }

  /**
   * Helper function to populate a featured/links property of action finder.
   *
   * @param array $referenced_entities
   *   Array that contains the featured/all actions referenced entities.
   * @param array $referenced_fields
   *   The array which contains the list of necessary fields from the
   *   referenced entities.
   *
   * @return array
   *   The variable structure for the featured/links property.
   */
  public static function populateActionFinderLinks(array $referenced_entities, array $referenced_fields) {
    // Populate links array.
    $links = [];
    if (!empty($referenced_entities)) {
      foreach ($referenced_entities as $item) {

        // Get the image, if there is one.
        $image = "";
        if (!empty($referenced_fields['image'])) {
          $is_image_field_populated = Helper::isFieldPopulated($item, $referenced_fields['image']);
          if ($is_image_field_populated) {
            $image = Helper::getFieldImageUrl($item, 'thumbnail_130x160', $referenced_fields['image']);
          }
        }

        // Get url + type from node external url field if exists and is
        // populated, otherwise from node url.
        $ext_url_field = "";
        if (!empty($referenced_fields['external'])) {
          $ext_url_field = $referenced_fields['external'];
        }
        $url = Helper::getEntityUrl($item, $ext_url_field);

        $links[] = [
          'image' => $image,
          'text' => $item->$referenced_fields['text']->value,
          'type' => $url['type'],
          'href' => $url['href'],
        ];
      }
    }

    return $links;
  }

  /**
   * Check for icon twig templates.
   *
   * @param string $icon
   *   The icon to render.
   *
   * @return string
   *   The path to the icon twig file.
   */
  public static function getIconPath($icon) {
    $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
    $path = DRUPAL_ROOT . '/' . $theme_path . '/patterns/atoms/';

    // Check if this template exists.
    if (file_exists($path . '07-user-added-icons/svg-' . strtolower($icon) . '.twig')) {
      return '@atoms/07-user-added-icons/svg-' . strtolower($icon) . '.twig';
    }

    if (file_exists($path . '05-icons/svg-' . strtolower($icon) . '.twig')) {
      return '@atoms/05-icons/svg-' . strtolower($icon) . '.twig';
    }

    if (file_exists($path . '06-icons-location/svg-loc-' . strtolower($icon) . '.twig')) {
      return '@atoms/06-icons-location/svg-' . strtolower($icon) . '.twig';
    }

    return '@atoms/05-icons/svg-marker.twig';
  }

  /**
   * Returns the current path alias.
   */
  public static function getCurrentPathAlias() {
    $path = \Drupal::service('path.current')->getPath();
    return \Drupal::service('path.alias_manager')->getAliasByPath($path);
  }

  /**
   * Returns the first line or paragraph of a string of text or raw HTML.
   *
   * @param string $string
   *   The text string or rawHTML string to be parsed.
   *
   * @return string
   *   The first line or paragraph of a string of text or raw HTML.
   */
  public static function getFirstParagraph($string) {
    if (!is_string($string)) {
      return FALSE;
    }

    // Get only the first html paragraph from the field value.
    if (preg_match("%(<p[^>]*>.*?</p>)%i", $string, $matches)) {
      return strip_tags($matches[1]);
    }

    // Get only the first plain text line.
    $plain_text_lines = preg_split("/\"\/\r\n|\r|\n\/\"/", $string);
    if ($plain_text_lines !== FALSE) {
      return $plain_text_lines[0];
    }

    return FALSE;
  }

  /**
   * Return a subset of a contactList data structure with primary phone/online.
   *
   * @param array $contact_list
   *   A contactList: see @organisms/by-author/contact-list.
   *
   * @return array
   *   A contactList array with only phone and online info for primary contact.
   */
  public static function getPrimaryContactPhoneOnlineContactList(array $contact_list) {
    // Build sidebar.contactList, a subset of pageContent.ContactList.
    $sidebar_contact = [];
    // Get the first contact point only.
    $contact_list['contacts'] = array_slice($contact_list['contacts'], 0, 1);
    // Make the contact render for sidebar contact list.
    $contact_list['contacts'][0]['accordion'] = FALSE;
    // Remove the address, fax, in person contact groups.
    foreach ($contact_list['contacts'][0]['groups'] as $key => $item) {
      if (in_array($item['name'], ['Address', 'Fax', 'In Person'])) {
        unset($contact_list['contacts'][0]['groups'][$key]);
      }
    }
    // If contact groups remain, they are online / phone, assign to return var.
    if (count($contact_list['contacts'][0]['groups']) > 0) {
      $sidebar_contact = $contact_list;
    }
    return $sidebar_contact;
  }

  /**
   * Remove the appended cache string from a URL.
   *
   * @param string $url
   *   The URL to be sanitized.
   * @param string $cacheString
   *   The string to be sanitized from the URL.
   *
   * @return string
   *   The sanitized URL.
   */
  public static function sanitizeUrlCacheString($url, $cacheString) {
    if (!is_string($url) || !is_string($cacheString)) {
      return FALSE;
    }

    $pos = strpos($url, $cacheString);
    if ($pos !== FALSE) {
      $url = substr($url, 0, $pos);
    }

    return $url;
  }

  /**
   * Supplements page meta data from metatags.
   *
   * @param array $metadata
   *   Array of pageMetaData used by templates/includes/page-meta.html.twig.
   * @param array $map
   *   Array that maps metatags to page_meta_data keys in form tag=>key.
   *   Defaults to 'siteDescription'=>'siteDescription'.
   * @param string $meta_area
   *   The part of the metadata attachments array to search in.
   *   Defaults to html_head.
   *
   * @return array
   *   The array with appended metatag values.
   */
  public static function addMetatagData(array $metadata, array $map = [], $meta_area = 'html_head') {
    // Code largely copied from metatag.module/metatag_preprocess_html()
    if (!function_exists('metatag_is_current_route_supported') || !metatag_is_current_route_supported()) {
      return $metadata;
    }

    if (empty($map)) {
      $map = [
        'siteDescription' => 'siteDescription',
        'description' => 'description',
      ];
    }

    $attachments = drupal_static('metatag_attachments');
    if (is_null($attachments)) {
      $attachments = metatag_get_tags_from_route();
    }

    if (!$attachments || empty($attachments['#attached'][$meta_area])) {
      return $metadata;
    }

    foreach ($attachments['#attached'][$meta_area] as $metatag) {
      $tag_name = $metatag[1];
      if (isset($map[$tag_name])) {
        // It's safe to access the value directly because it was already
        // processed in MetatagManager::generateElements().
        $metadata[$map[$tag_name]] = $metatag[0]['#attributes']['content'];
      }
    }

    return $metadata;
  }

  /**
   * Returns the center lat/lng of a map.
   *
   * @param array $data
   *   Array of coords for each marker.
   *
   * @return array
   *   Return an array with center lat and lng.
   */
  public static function getCenterFromDegrees(array $data) {
    if (!is_array($data)) {
      return FALSE;
    }
    $num_coords = count($data);
    $iX = 0.0;
    $iY = 0.0;
    $iZ = 0.0;
    foreach ($data as $coord) {
      $lat = $coord[0] * pi() / 180;
      $lon = $coord[1] * pi() / 180;
      $a = cos($lat) * cos($lon);
      $b = cos($lat) * sin($lon);
      $c = sin($lat);
      $iX += $a;
      $iY += $b;
      $iZ += $c;
    }
    $iX /= $num_coords;
    $iY /= $num_coords;
    $iZ /= $num_coords;
    $lon = atan2($iY, $iX);
    $hyp = sqrt($iX * $iX + $iY * $iY);
    $lat = atan2($iZ, $hyp);
    return [
      $lat * 180 / pi(),
      $lon * 180 / pi(),
    ];
  }

  /**
   * Helper function to prepare the Hours.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param array $options
   *   The object that contains static data and other options..
   *
   * @return array
   *   Return an array of items that contain:
   *    "office hours": {
   *    }
   */

  /**
   * Helper function to build Hours section.
   *
   * @param object $hours
   *   Send a field object.
   * @param string $title
   *   Which generates the heading before a section.
   *
   * @return array
   *   Return structured array.
   */
  public static function buildHours($hours, $title) {
    $rteElements = [];

    // Hours section.
    foreach ($hours as $index => $hour) {
      $entity = $hour->entity;

      if (!method_exists($entity, 'hasField')) {
        return FALSE;
      }

      // Creates a map of fields that are on the entitiy.
      $map = [
        'label' => ['field_label', 'field_hours_group_title'],
        'time' => ['field_time_frame', 'field_hours_structured'],
        'hour' => ['field_hours', 'field_hours_description'],
        'description' => ['field_hours_description'],
      ];

      // Determines which fieldnames to use from the map.
      $field = Helper::getMappedFields($entity, $map);
      $hours_render_array = $entity->field_hours_structured->view('full');

      $rteElements[] = [
        'path' => '@atoms/04-headings/heading-4.twig',
        'data' => [
          'heading4' => [
            'text' => Helper::fieldValue($entity, $field['label']),
          ],
        ],
      ];

      $rteElements[] = [
        'path' => '@atoms/11-text/paragraph.twig',
        'data' => [
          'paragraph' => [
            'text' => $hours_render_array,
          ],
        ],
      ];

      if (Helper::isFieldPopulated($entity, $field['description'])) {
        $rteElements[] = [
          'path' => '@atoms/11-text/paragraph.twig',
          'data' => [
            'paragraph' => [
              'text' => Helper::fieldValue($entity, $field['description']),
            ],
          ],
        ];
      }
    }

    return [
      'title' => $title,
      'into' => '',
      'id' => Helper::createIdTitle($title),
      'path' => '@organisms/by-author/rich-text.twig',
      'data' => [
        'richText' => [
          'property' => '',
          'rteElements' => $rteElements,
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required for richText.
   *
   * @param array $elements
   *   An array of elements.
   *
   * @see @organsms/by-author/rich-text.twig
   *
   * @return array
   *   'path' => '@organisms/by-author/rich-text.twig',
   *     'data' => [
   *       'richText' => [
   *         'rteElements' => array of rteElements,
   *       ],
   *     ],
   *   ]
   */
  public static function prepareRichTextElements(array $elements) {
    if (!is_array($elements)) {
      return [];
    }
    return [
      'path' => '@organisms/by-author/rich-text.twig',
      'data' => [
        'richText' => [
          'rteElements' => $elements,
        ],
      ],
    ];
  }

  /**
   * Return the data structure for a link based on a given entity.
   *
   * @param object $entity
   *   The object for which we want the link href, type, text, image, and label.
   *
   * @return array
   *   A an array with structure for illustrated, callout, or decorative link:
   *    [
   *      'href' => 'http://path/to/entity',
   *      'type' => 'internal' || 'external',
   *      'title' => 'My Entity Title',
   *      'image' => 'http://path/to/image',
   *      'label' => 'Guide:', etc.
   *    ]
   */
  public static function createLinkFromEntity($entity) {

    // Creates a map of fields that are on the referenced entity.
    $map = [
      'image' => ['field_photo', 'field_guide_page_bg_wide'],
      'text' => ['title', 'field_title'],
      'external' => ['field_external_url'],
      'href' => [],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Get the image, if there is one.
    $image = "";
    if (!empty($fields['image'])) {
      $is_image_field_populated = Helper::isFieldPopulated($entity, $fields['image']);
      if ($is_image_field_populated) {
        $image = Helper::getFieldImageUrl($entity, 'thumbnail_130x160', $fields['image']);
      }
    }

    // Get url + type from node external url field if exists and is
    // populated, otherwise from node url.
    $ext_url_field = "";
    if (!empty($fields['external'])) {
      $ext_url_field = $fields['external'];
    }
    $url = Helper::getEntityUrl($entity, $ext_url_field);

    $label = '';
    if ($entity->getType() === 'guide_page' || $entity->getType() === 'stacked_layout') {
      $label = "Guide:";
    }

    $link = [
      'image' => $image,
      'text' => Helper::fieldValue($entity, $fields['text']),
      'type' => $url['type'],
      'href' => $url['href'],
      'label' => $label,
    ];

    return $link;
  }

  /**
   * Return an array of links: decorative, callout or illustrated.
   *
   * @param object $entity
   *   The entity which contains the entity reference or link field.
   * @param object $field
   *   The field which contains or refers to the link information.
   *
   * @return array
   *   Returns an array with the following structure:
   *    [
   *      [
   *        'href' => 'http://path/to/entity',
   *        'type' => 'internal' || 'external',
   *        'title' => 'My Entity Title',
   *        'image' => 'http://path/to/image',
   *        'label' => 'Guide:', etc.
   *      ], ...
   *    ]
   */
  public static function createIllustratedOrCalloutLinks($entity, $field) {
    // Check if the target field is entity reference, else assume link field.
    if (Helper::isEntityReferenceField($entity, $field)) {
      // Retrieves the entities referenced from the entity field.
      $referenced_entities = Helper::getReferencedEntitiesFromField($entity, $field);

      // Populate a section (featured links or links).
      $links = array_map(['Drupal\mayflower\Helper', 'createLinkFromEntity'], $referenced_entities);
    }
    else {
      $links = Helper::separatedLinks($entity, $field);
    }

    return $links;
  }

  /**
   * Returns SEO title.
   *
   * @param string $element
   *   An element.
   *
   * @return string
   *   A well processed link id.
   */
  public static function createIdTitle($element) {
    return strtolower(preg_replace('/-+/', '-', preg_replace('/[^\wáéíóú]/', '-', $element)));
  }

  /**
   * Return pageHeader.optionalContents structure populated with contactUs.
   *
   * @param object $entity
   *   The entity which contains the entity reference or link field.
   * @param object $field
   *   The field which contains or refers to the link information.
   * @param array $options
   *   An array of options for header contact.
   *
   * @see @molecules/contact-us.twig
   * @see @organisms/by-template/page-header.twig
   *
   * @return array
   *   Returns an array with the following structure:
   *   [ [
   *       'path' => '@molecules/contact-us.twig',
   *       'data' => [
   *         'contactUs' => [ contact us data structure ]
   *       ],
   *     ], ... ]
   */
  public static function buildPageHeaderOptionalContentsContactUs($entity, $field, array $options = []) {
    $optionalContentsContactUs = [];
    $contactUs = [];
    $contact_items = Helper::getReferencedEntitiesFromField($entity, $field);

    if (!empty($contact_items)) {
      foreach ($contact_items as $contact_item) {
        $contactUs = Molecules::prepareContactUs($contact_item, $options + ['display_title' => FALSE]);
      }

      $optionalContentsContactUs[] = [
        'path' => '@molecules/contact-us.twig',
        'data' => ['contactUs' => $contactUs],
      ];
    }

    return $optionalContentsContactUs;
  }

  /**
   * Return structure necessary for either sidebar or comp heading.
   *
   * @param array $options
   *   Array of options.
   *   [
   *     'type' => 'compHeading' || 'sidebarHeading' || 'coloredHeading',
   *     'title' => 'My title text' / required,
   *     'sub' => [required if TRUE],
   *     'centered' => [required if TRUE],
   *     'color' => [required if 'green', 'yellow'],
   *   ].
   *
   * @return array
   *   The data structure for either comp or sidebar heading.
   */
  public static function buildHeading(array $options) {
    $heading_type = isset($options['type']) ? $options['type'] : 'compHeading';

    $heading = [
      $heading_type => [
        'title' => isset($options['title']) ? $options['title'] : '',
        'text' => isset($options['title']) ? $options['title'] : '',
        'sub' => isset($options['sub']) ? $options['sub'] : FALSE,
        'color' => isset($options['color']) ? $options['color'] : '',
        'id' => isset($options['title']) ? Helper::createIdTitle($options['title']) : '',
        'centered' => isset($options['centered']) ? $options['centered'] : '',
      ],
    ];

    return $heading;
  }

  /**
   * Returns whether or not the entity's field is an entity reference field.
   *
   * @param object $entity
   *   The object that has the field which we are checking.
   * @param string $field
   *   The name of the field which we are checking.
   *
   * @return bool
   *   Whether or not this entity's field is entity reference.
   */
  public static function isEntityReferenceField($entity, $field) {
    return $entity->getFieldDefinition($field)->getType() === 'entity_reference';
  }

  /**
   * Returns a formatted address from entity.
   *
   * @param object $addressEntity
   *   The object that contains the field.
   * @param array $options
   *   An array of options.
   *
   * @return string
   *   A flattened string of address info.
   */
  public static function formatAddress($addressEntity, array $options = []) {
    $address = '';

    if (isset($addressEntity[0])) {
      // Add address module fields.
      $address = !empty($addressEntity[0]->address_line1) ? $addressEntity[0]->address_line1 . ', ' : '';
      // If we're in the sidebar add a newline.
      if ($options['sidebar']) {
        $address .= PHP_EOL;
      }
      $address .= !empty($addressEntity[0]->address_line2) ? $addressEntity[0]->address_line2 . ', ' : '';
      // If we're in the sidebar add a newline.
      if ($options['sidebar']) {
        $address .= PHP_EOL;
      }
      $address .= !empty($addressEntity[0]->locality) ? $addressEntity[0]->locality : '';
      $address .= !empty($addressEntity[0]->administrative_area) ? ', ' . $addressEntity[0]->administrative_area : '';
      $address .= !empty($addressEntity[0]->postal_code) ? ' ' . $addressEntity[0]->postal_code : '';
    }

    return $address;
  }

}
