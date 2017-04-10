<?php

namespace Drupal\mayflower\Prepare;

use Drupal\mayflower\Helper;
use Drupal\Component\Utility\UrlHelper;
use Drupal\file\Entity\File;

/**
 * Provides variable structure for mayflower molecules using prepare functions.
 *
 * Copyright 2017 Palantir.net, Inc.
 */
class Molecules {

  /**
   * Returns the actionSeqList variable structure for the mayflower template.
   *
   * @param object $entity
   *   The object that contains the fields for the sequential list.
   *
   * @see @molecules/action-sequential-list.twig
   *
   * @return array
   *   Returns an array of elements that contains:
   *   [[
   *     "title": "My Title",
   *     "rteElements": [[
   *       "path": "@atoms/11-text/paragraph.twig",
   *       "data": [
   *         "paragraph": [
   *           "text": "My Paragraph Text"
   *         ]
   *       ]
   *     ], ...]
   *   ], ...]
   */
  public static function prepareActionSeqList($entity) {
    $actionSeqLists = [];

    // Creates a map of fields on the parent entity.
    $map = [
      'reference' => ['field_action_step_numbered_items'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Retrieves the referenced field from the entity.
    $items = Helper::getReferencedEntitiesFromField($entity, $fields['reference']);

    // Creates a map of fields that are on the referenced entitiy.
    $referenced_fields_map = [
      'title' => ['field_title'],
      'content' => ['field_content'],
    ];

    // Determines the fieldsnames to use on the refrenced entity.
    $referenced_fields = Helper::getMappedReferenceFields($items, $referenced_fields_map);

    // Creates the actionSeqLists array structure.
    if (!empty($items)) {
      foreach ($items as $id => $item) {
        $actionSeqLists[$id] = [];
        $actionSeqLists[$id]['title'] = Helper::fieldFullView($item, $referenced_fields['title']);
        $actionSeqLists[$id]['rteElements'][] = [
          'path' => '@atoms/11-text/paragraph.twig',
          'data' => [
            'paragraph' => [
              'text' => Helper::fieldFullView($item, $referenced_fields['content']),
            ],
          ],
        ];
      }
    }

    return $actionSeqLists;
  }

  /**
   * Returns the variables structure required to render calloutLinks template.
   *
   * @param object $entity
   *   The object that contains the link field.
   *
   * @see @molecules/callout-links.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "text": "Order a MassParks Pass online through Reserve America",
   *      "type": internal/external,
   *      "href": URL,
   *      "info": ""
   *    ], ...]
   */
  public static function prepareCalloutLinks($entity) {
    $map = [
      'link' => ['field_link'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Creates array of links to use in calloutLinks.
    $calloutLinks = Helper::separatedLinks($entity, $fields['link']);

    return $calloutLinks;
  }

  /**
   * Returns the variables structure required to render icon links.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   An array containing options.
   *
   * @see @molecules/icon-links.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    "iconLinks": [
   *      "items":[[
   *        "icon": /@path/to/icon.twig
   *        "link": [
   *          "href": "https://twitter.com/MassHHS",
   *          "text": "@MassHHS",
   *          "chevron": ""
   *        ]
   *      ], ...]
   *    ]
   */
  public static function prepareIconLinks($entity, array $options = []) {
    $items = [];
    $map = [
      'socialLinks' => ['field_social_links', 'field_services_social_links'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Creates array of links with link parts.
    $links = Helper::separatedLinks($entity, $fields['socialLinks']);

    // Get icons for social links.
    $services = [
      'twitter',
      'facebook',
      'flickr',
      'blog',
      'linkedin',
      'google',
      'instagram',
    ];

    foreach ($links as $link) {
      $icon = '';

      foreach ($services as $key => $service) {
        if (strpos($link['href'], $service) !== FALSE) {
          $icon = $services[$key];
          break;
        }
      }

      $items[] = [
        'icon' => Helper::getIconPath($icon),
        'link' => $link,
      ];
    }

    return [
      'iconLinks' => [
        'items' => $items,
      ],
    ];
  }

  /**
   * Returns the variables structure required to render sectionLinks template.
   *
   * @param object $entity
   *   The object that contains the title/lede fields.
   * @param array $links
   *   The array of links.
   *
   * @see @molecules/section-links.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    sectionLinks: {
   *      catIcon: {
   *        icon:
   *        type: string/path to icon
   *        small:
   *        type: boolean/true
   *      },
   *      title: {
   *        href:
   *        type: url/required
   *        text:
   *        type: string/required
   *      },
   *      description:
   *      type: string
   *      links: [{
   *        href:
   *        type: url/required
   *        text:
   *        type: string/required
   *      }]
   *    }
   */
  public static function prepareSectionLink($entity, array $links) {
    $index = &drupal_static(__FUNCTION__);
    $index++;
    return [
      'id' => 'section_link_' . $index,
      'catIcon' => [
        'icon' => Helper::getIconPath($entity->field_icon_term->referencedEntities()[0]->get('field_sprite_name')->value),
        'small' => 'true',
      ],
      'title' => [
        'href' => '#',
        'text' => $entity->getTitle(),
      ],
      'description' => $entity->field_lede->value,
      'links' => $links,
    ];
  }

  /**
   * Returns the variables structure required to render alerts.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param string $field
   *   The field name.
   *
   * @see @molecules/callout-alert.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareCalloutAlert($entity, $field) {
    $map = [
      'field' => [$field],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    return [
      'path' => '@organisms/by-author/callout-alert.twig',
      'data' => [
        'calloutAlert' => [
          'decorativeLink' => [
            'href' => '',
            'text' => Helper::fieldValue($entity, $fields['field']),
          ],
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required to render contactGroup template.
   *
   * @param array $entities
   *   An array that containing the $entities for the group.
   * @param array $options
   *   An array containing options.
   *   array(
   *     type: string ('phone' || 'online' || 'email' || 'address' || 'fax')
   *   )
   * @param array &$contactInfo
   *   An array that containing the current schema contact info.
   *
   * @see @molecules/contact-group.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    contactGroup: {
   *      icon: string / path to icon,
   *      name: string ('Phone' || 'Online' || 'Address' || 'Fax') / optional
   *      items: [{
   *        type: string ('phone' || 'online' || 'email' || 'address' || 'fax' )
   *        property: string / optional
   *        label: string / optional
   *        value: string (html allowed) / required
   *        link: string / optional
   *        details: string / optional
   *      }]
   *    }
   */
  public static function prepareContactGroup(array $entities, array $options, array &$contactInfo) {
    $type = $options['type'];

    switch ($type) {
      case 'address':
        $name = t('Address');
        $icon = '@atoms/05-icons/svg-marker.twig';
        break;

      case 'online':
        $name = t('Online');
        $icon = '@atoms/05-icons/svg-laptop.twig';
        break;

      case 'fax':
        $name = t('Fax');
        $icon = '@atoms/05-icons/svg-fax-icon.twig';
        break;

      case 'phone':
        $name = t('Phone');
        $icon = '@atoms/05-icons/svg-phone.twig';
        break;

      default:
        $name = '';
        $icon = '';
        break;

    }

    $contactGroup = [
      'name' => $name,
      'icon' => $icon,
      'hidden' => '',
      'items' => [],
    ];

    foreach ($entities as $entity) {
      $item = [];

      $item['type'] = $type;

      // Creates a map of fields that are on the entitiy.
      $map = [
        'details' => ['field_caption'],
        'label' => ['field_label'],
        'value' => ['field_address_text', 'field_phone', 'field_fax'],
        'link' => ['field_link_single', 'field_email'],
      ];

      // Determines which fieldnames to use from the map.
      $fields = Helper::getMappedFields($entity, $map);

      if (array_key_exists('details', $fields) && Helper::isFieldPopulated($entity, $fields['details'])) {
        $item['details'] = Helper::fieldFullView($entity, $fields['details']);
      }

      if (array_key_exists('label', $fields) && Helper::isFieldPopulated($entity, $fields['label'])) {
        $item['label'] = Helper::fieldFullView($entity, $fields['label']);
      }

      if ($type == 'address') {
        $item['value'] = Helper::fieldFullView($entity, $fields['value']);
        $item['link'] = 'https://maps.google.com/?q=' . urlencode(Helper::fieldValue($entity, $fields['value']));

        // Respect first address provided if present.
        if (!$contactInfo['address']) {
          $contactInfo['address'] = Helper::fieldValue($entity, $fields['value']);
          $contactInfo['hasMap'] = $item['link'];
        }
      }
      elseif ($type == 'fax' || $type == 'phone') {
        $item['value'] = Helper::fieldValue($entity, $fields['value']);
        $item['link'] = str_replace(['+', '-'], '', filter_var(Helper::fieldValue($entity, $fields['value']), FILTER_SANITIZE_NUMBER_INT));

        // Respect first fax and phone number provided if present.
        if (!$contactInfo[$type]) {
          $contactInfo[$type] = "+1" . $item['link'];
        }
      }
      elseif ($type == 'online') {
        if ($entity->getType() == 'online_email') {
          $link = Helper::separatedEmailLink($entity, $fields['link']);
          $item['link'] = $link['href'];
          $item['value'] = $link['text'];
          $item['type'] = 'email';

          // Respect first email address provided if present.
          if (!$contactInfo['email']) {
            $contactInfo['email'] = $item['link'];
          }
        }
        else {
          $link = Helper::separatedLinks($entity, $fields['link']);
          $item['link'] = $link[0]['href'];
          $item['value'] = $link[0]['text'];
        }
      }

      $contactGroup['items'][] = $item;
    }

    return $contactGroup;
  }

  /**
   * Returns the variables structure required to render calloutLinks template.
   *
   * @param object $entity
   *   The object that contains the link field.
   * @param array $options
   *   An array containing options.
   *   array(
   *     display_title: Boolean / require.
   *   )
   *
   * @see @molecules/contact-us.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    contactUs: array(
   *      schemaSd: array(
   *        property: string / required,
   *        type: string / required,
   *      ),
   *     title: array(
   *       href: string (url) / optional
   *       text: string (_blank || '') / optional
   *       chevron: boolean / required
   *     ),
   *     groups: [
   *       contactGroup see @molecules/contact-group
   *     ]
   *   )
   */
  public static function prepareContactUs($entity, array $options) {
    $title = '';

    // Create contactInfo object for governmentOrg schema.
    $contactInfo = [
      "address" => "",
      "hasMap" => "",
      "phone" => "",
      "fax" => "",
      "email" => "",
    ];

    // Creates a map of fields that are on the entitiy.
    $reference_map = [
      'address' => ['field_ref_address'],
      'phone' => ['field_ref_phone_number'],
      'online' => ['field_ref_links'],
      'fax' => ['field_ref_fax_number'],
    ];

    $map = [
      'title' => ['field_display_title'],
    ];

    // Determines which fieldnames to use from the map.
    $referenced_fields = Helper::getMappedFields($entity, $reference_map);

    $groups = [];

    foreach ($referenced_fields as $id => $field) {
      if (Helper::isFieldPopulated($entity, $field)) {
        $items = Helper::getReferencedEntitiesFromField($entity, $field);
        $groups[] = Molecules::prepareContactGroup($items, ['type' => $id], $contactInfo);
      }
    }

    $fields = Helper::getMappedFields($entity, $map);

    $display_title = $options['display_title'];

    if (isset($fields['title']) && Helper::isFieldPopulated($entity, $fields['title']) && $display_title != FALSE) {
      $title = [
        'href' => $entity->toURL()->toString(),
        'text' => $entity->$fields['title']->value,
        'chevron' => FALSE,
      ];
    }

    return [
      'schemaSd' => [
        'property' => 'containedInPlace',
        'type' => 'CivicStructure',
      ],
      'schemaContactInfo' => $contactInfo,
      // TODO: Needs validation if empty or not.
      'title' => $title,
      'groups' => $groups,
    ];
  }

  /**
   * Returns the variables structure required to render key actions.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param string $field
   *   The field name.
   * @param array $options
   *   An array of options.
   *
   * @see @molecules/callout-alert.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareKeyActions($entity, $field = '', array $options = []) {
    $map = [
      'field' => [$field],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Roll up our Key Action links.
    $links = Helper::separatedLinks($entity, $fields['field']);

    return [
      'path' => '@organisms/by-author/key-actions.twig',
      'data' => [
        'keyActions' => [
          'compHeading' => [
            'title' => $options['title'],
            'sub' => TRUE,
          ],
          'links' => $links,
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required to render actionActivities.
   *
   * @param object $entities
   *   An EntityReferenceRevisionsFieldItemList that contains the entities.
   *
   * @see @molecules/action-activities.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "title": "Order a MassParks Pass online through Reserve America",
   *      "into": "",
   *      "id": "unique identifier",
   *      "path": ""
   *      "data": ""
   *    ], ...]
   */
  public static function prepareActionActivities($entities) {
    $actionActivities = [];

    // Activities section.
    foreach ($entities as $entity) {
      $activityEntity = $entity->entity;

      // Creates a map of fields that are on the entitiy.
      $map = [
        'image' => ['field_image'],
        'title' => ['field_title'],
        'lede' => ['field_lede'],
      ];

      // Determines which fieldnames to use from the map.
      $fields = Helper::getMappedFields($activityEntity, $map);

      $actionActivities[] = [
        'image' => Helper::getFieldImageUrl($activityEntity, 'activities_image', $fields['image']),
        'alt' => $activityEntity->$fields['image']->alt,
        'title' => Helper::fieldValue($activityEntity, $fields['title']),
        'description' => Helper::fieldValue($activityEntity, $fields['lede']),
        'linkTitle' => '',
        'href' => '',
      ];
    }

    return [
      'title' => t('Activities'),
      'into' => '',
      'id' => t('activities'),
      'path' => '@molecules/action-activities.twig',
      'data' => [
        'actionActivities' => $actionActivities,
      ],
    ];
  }

  /**
   * Returns the variables structure required to render callout stats.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param string $field
   *   The field name.
   * @param array $options
   *   An array of options.
   *
   * @see @molecules/callout-stats.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareCalloutStats($entity, $field = '', array $options = []) {
    $map = [
      'field' => [$field],
      'label' => ['field_guide_section_label'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    return [
      'path' => '@molecules/callout-stats.twig',
      'data' => [
        'statsCallout' => [
          'pull' => $options['pull'],
          'stat' => Helper::fieldValue($entity, $fields['field']),
          'content' => Helper::fieldValue($entity, $fields['label']),
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required to render googleMap.
   *
   * @param array $entities
   *   The object that contains the fields.
   *
   * @see @molecules/google-map.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "map": "Order a MassParks Pass online through Reserve America",
   *      "markers": "",
   *    ], ...]
   */
  public static function prepareGoogleMapFromContacts(array $entities) {

    $phone_numbers = [];
    $fax_numbers = [];
    $markers = [];
    $links = [];
    $index = 0;

    foreach ($entities as $entity) {

      $map_ref = [
        'phone_numbers' => ['field_ref_phone_number'],
        'fax_numbers' => ['field_ref_fax_number'],
        'links' => ['field_ref_links'],
        'addresses' => ['field_ref_address'],
      ];

      // Determines which field names to use from the map.
      $fields = Helper::getMappedFields($entity, $map_ref);

      // Get phone numbers.
      foreach ($entity->$fields['phone_numbers'] as $phone) {
        $phoneEntity = $phone->entity;

        // Creates a map of fields that are on the entitiy.
        $map = [
          'phone' => ['field_phone'],
        ];

        // Determines which fieldnames to use from the map.
        $field = Helper::getMappedFields($phoneEntity, $map);
        $phone_numbers[] = Helper::fieldValue($phoneEntity, $field['phone']);
      }

      // Get fax numbers.
      foreach ($entity->$fields['fax_numbers'] as $fax) {
        $faxEntity = $fax->entity;

        // Creates a map of fields that are on the entitiy.
        $map = [
          'fax' => ['field_fax'],
        ];

        // Determines which fieldnames to use from the map.
        $field = Helper::getMappedFields($faxEntity, $map);
        $fax_numbers[] = Helper::fieldValue($faxEntity, $field['fax']);
      }

      // Get links.
      foreach ($entity->$fields['links'] as $link) {
        foreach ($link->entity->field_link_single as $linkData) {
          $links[] = $linkData->getValue()['title'];
        }
      }

      // Get Address and Map info.
      foreach ($entity->$fields['addresses'] as $address) {
        $addressEntity = $address->entity;

        // Creates a map of fields that are on the entitiy.
        $map = [
          'lat_lng' => ['field_lat_long'],
          'address' => ['field_address_text'],
          'label' => ['field_label'],
        ];

        // Determines which fieldnames to use from the map.
        $address_fields = Helper::getMappedFields($addressEntity, $map);

        $data[] = [
          0 => $addressEntity->$address_fields['lat_lng']->lat,
          1 => $addressEntity->$address_fields['lat_lng']->lon,
        ];

        $markers[] = [
          'position' => [
            'lat' => $addressEntity->$address_fields['lat_lng']->lat,
            'lng' => $addressEntity->$address_fields['lat_lng']->lon,
          ],
          'infoWindow' => [
            'name' => Helper::fieldValue($addressEntity, $address_fields['label']),
            'phone' => isset($phone_numbers[$index]) ? $phone_numbers[$index] : '',
            'fax' => isset($fax_numbers[$index]) ? $fax_numbers[$index] : '',
            'email' => isset($links[$index]) ? $links[$index] : '',
            'address' => Helper::fieldValue($addressEntity, $address_fields['address']),
          ],
          'label' => ++$index,
        ];

        // Since we just want to display the FIRST Address and info.
        // This was an addition.
        break;
      }
    }

    // mapProp.
    $actionMap['map']['zoom'] = 12;

    if (empty($data)) {
      return [];
    }

    $centers = Helper::getCenterFromDegrees($data);

    $actionMap['map']['center'] = [
      'lat' => $centers[0],
      'lng' => $centers[1],
    ];

    $actionMap['markers'] = $markers;

    return $actionMap;
  }

  /**
   * Returns the variables structure required to render googleMap.
   *
   * @param array $entities
   *   An array of entities.
   *
   * @see @molecules/google-map.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "map": "Order a MassParks Pass online through Reserve America",
   *      "markers": "",
   *    ], ...]
   */
  public static function prepareGoogleMap(array $entities) {
    $markers = [];

    foreach ($entities as $index => $marker) {
      $data[] = [
        0 => $marker->lat,
        1 => $marker->lon,
      ];

      $markers[] = [
        'position' => [
          'lat' => $marker->lat,
          'lng' => $marker->lon,
        ],
        'label' => ++$index,
        'infoWindow' => [
          'name' => $marker->name,
          'phone' => '',
          'fax' => '',
          'email' => '',
          'address' => '',
        ],
      ];
    }

    // mapProp.
    $actionMap['map']['zoom'] = 12;

    if (empty($data)) {
      return [];
    }

    $centers = Helper::getCenterFromDegrees($data);

    $actionMap['map']['center'] = [
      'lat' => $centers[0],
      'lng' => $centers[1],
    ];

    $actionMap['markers'] = $markers;

    return $actionMap;
  }

  /**
   * Returns the variables structure required to render googleMapSection.
   *
   * @param object $entity
   *   The object that contains the fields.
   *
   * @see @molecules/action-map.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "path": "@molecules/action-map.twig",
   *      "data": "[actionMap",
   */
  public static function prepareGoogleMapSection($entity) {
    return [
      'title' => '',
      'into' => '',
      'id' => '',
      'path' => '@molecules/action-map.twig',
      'data' => [
        'actionMap' => Molecules::prepareGoogleMap($entity),
      ],
    ];
  }

  /**
   * Returns the variables structure required to render widgets.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param string $type
   *   The type of widget to produce.
   *
   * @see @molecules/action-WIDGET.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "path": "@molecules/action-WIDGET.twig",
   *      "data": [ 'widget' ],
   *    ], ...]
   */
  public static function prepareWidgets($entity, $type) {
    $widgets = [];

    // Create widgets.
    foreach ($entity as $widget) {
      $widgets[] = [
        'path' => '@molecules/action-' . $type . '.twig',
        'data' => [
          'action' . $type => [
            'name' => [
              'type' => '',
              'href' => '#',
              'text' => '',
              'property' => '',
            ],
            'date' => '',
            'description' => '',
          ],
        ],
      ];
    }

    return $widgets;
  }

  /**
   * Returns the variables structure required to render locationIcons.
   *
   * @param object $entity
   *   The object that contains the fields.
   *
   * @see @molecules/location-icons.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [[
   *      "path": "@molecules/icons.twig",
   *      "name": "Taxo name",
   *    ], ...]
   */
  public static function prepareLocationIcons($entity) {
    $icons = [];
    $map = [
      'icons' => ['field_location_icons'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Roll up icons from taxo.
    foreach ($entity->$fields['icons'] as $icon) {
      $icons[] = [
        'path' => Helper::getIconPath($icon->entity->get('field_sprite_name')->value),
        'name' => $icon->entity->getName(),
      ];
    }
    return $icons;
  }

  /**
   * Returns the variables structure required to render key actions.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param string $field
   *   The field name.
   * @param array $options
   *   An array of options.
   *
   * @see @molecules/callout-time.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareCalloutTime($entity, $field = '', array $options = []) {
    $map = [
      'field' => [$field],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    return [
      'path' => '@organisms/by-author/callout-time.twig',
      'data' => [
        'calloutTime' => [
          'text' => Helper::fieldValue($entity, $fields['field']),
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required to render action downloads.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param array $options
   *   An array of options.
   *
   * @see @molecules/action-downloads.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareActionDownloads($entity, array $options = []) {
    $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
    $path = DRUPAL_ROOT . '/' . $theme_path . '/patterns/atoms/';

    $map = [
      'downloads' => ['field_guide_section_downloads', 'field_service_file'],
      'link' => ['field_guide_section_link', 'field_service_links'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    if (array_key_exists('link', $fields)) {
      foreach ($entity->$fields['link'] as $link) {
        $actionDownloads[] = [
          'icon' => '@atoms/05-icons/svg-laptop.twig',
          'text' => $link->getValue()['title'],
          'href' => $link->getUrl()->toString(),
          'type' => (UrlHelper::isExternal($link->getUrl()->toString())) ? 'external' : 'internal',
          'size' => '',
          'format' => 'form',
        ];
      }
    }

    // Default icon.
    $icon = '@atoms/05-icons/svg-doc-generic.twig';

    // Roll up our Action Downloads.
    foreach ($entity->$fields['downloads'] as $file) {
      $fileEntity = $file->entity;

      $file = ($fileEntity instanceof File) ? $file : File::load($fileEntity->field_upload_file->target_id);

      // Get file info.
      $bytes = $file->getSize();
      $readable_size = format_size($bytes);
      $filename = $file->getFilename();
      $file_info = new \SplFileInfo($filename);
      $file_extension = $file_info->getExtension();
      $url = file_create_url($file->getFileUri());

      // Check if icon template exists.
      if (file_exists($path . '05-icons/svg-doc-' . strtolower($file_extension) . '.twig')) {
        $icon = '@atoms/05-icons/svg-doc-' . $file_extension . '.twig';
      }

      $actionDownloads[] = [
        'icon' => $icon,
        'text' => $fileEntity->field_title->value,
        'href' => $url,
        'type' => '',
        'size' => strtoupper($readable_size),
        'format' => strtoupper($file_extension),
      ];
    }

    return $actionDownloads;
  }

}
