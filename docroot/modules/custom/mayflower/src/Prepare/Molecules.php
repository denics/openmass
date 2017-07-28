<?php

namespace Drupal\mayflower\Prepare;

use Drupal\mayflower\Helper;
use Drupal\file\Entity\File;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use Drupal\media_entity\Entity\Media;

/**
 * Provides variable structure for mayflower molecules using prepare functions.
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
        $actionSeqLists[$id]['richText']['rteElements'][] = [
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
   * Returns the actionStep variable structure.
   *
   * @param object $entity
   *   The object that contains the fields for the sequential list.
   * @param array $referenced_fields
   *   The reference fields on the entity.
   * @param array $options
   *   The static options for action steps: accordion, isExpanded, etc.
   *
   * @see @molecules/action-step.twig
   *
   * @return array
   *   Return a structured array.
   */
  public static function prepareActionStep($entity, array $referenced_fields, array $options) {
    $downloadLinks = [];

    $actionStep = [
      'accordion' => isset($options['accordion']) ? $options['accordion'] : FALSE,
      'isExpanded' => isset($options['expanded']) ? $options['expanded'] : TRUE,
      'accordionLabel' => isset($options['label']) ? $options['label'] : '',
      'icon' => isset($options['icon_path']) ? $options['icon_path'] : '',
      'title' => Helper::fieldFullView($entity, $referenced_fields['title']),
      'richText' => [
        'rteElements' => [
          Atoms::prepareRawHtml($entity, ['field' => $referenced_fields['content']]),
        ],
      ],
    ];

    if (array_key_exists('downloads', $referenced_fields)) {
      $downloadLinks = Helper::isFieldPopulated($entity, $referenced_fields['downloads']) ? Organisms::prepareFormDownloads($entity) : [];
    }

    if (array_key_exists('more_link', $referenced_fields)) {
      $actionStep['decorativeLink'] = Helper::isFieldPopulated($entity, $referenced_fields['more_link']) ? Helper::separatedLink($entity->get($referenced_fields['more_link'])[0]) : [];
    }

    return array_merge($actionStep, $downloadLinks);
  }

  /**
   * Returns the imagePromo variable structure.
   *
   * @param object $entity
   *   The object that contains the fields for the sequential list.
   * @param array $fields
   *   The reference fields on the entity.
   * @param array $options
   *   Allow the title to be set via an options array.
   *
   * @see @molecules/image-promo.twig
   *
   * @return array
   *   Return a structured array.
   */
  public static function prepareImagePromo($entity, array $fields, array $options) {
    $imagePromo = [];
    $href = !empty($entity->$fields['link']->entity) ? $entity->$fields['link']->entity->toURL()->toString() : [];
    $title = '';

    if (array_key_exists('image', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['image'])) {
        $src = '';

        if (Helper::fieldValue($entity, $fields['image'])) {
          $src = Helper::fieldValue($entity, $fields['image']);
          $src = file_create_url($src);
        }
        else {
          $src = Helper::getFieldImageUrl($entity, 'activities_image', $fields['image']);
        }

        $imagePromo['image'] = [
          'src' => $src,
          'alt' => $entity->$fields['image']->alt,
          'href' => $href,
        ];

      }
    }

    if (array_key_exists('title', $options)) {
      $title = $options['title'];
    }
    elseif (array_key_exists('title', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['title'])) {
        $title = Helper::fieldValue($entity, $fields['title']);
      }
    }

    $imagePromo['title'] = [
      'text' => $title,
      'href' => $href,
    ];

    if (array_key_exists('lede', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['lede'])) {
        $imagePromo['description'] = [
          'richText' => Atoms::preparePageContentParagraph($entity->$fields['lede']),
        ];
      }
    }

    if (array_key_exists('link', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['link'])) {
        $imagePromo['link'] = [
          'text' => 'More',
          'href' => $href,
          'type' => 'chevron',
          'info' => t('Read More about @activity', ['@activity' => $title]),
        ];
      }
    }

    return $imagePromo;
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
      'medium',
      'youtube',
    ];

    foreach ($links as $link) {
      $icon = '';

      foreach ($services as $key => $service) {
        if (strpos($link['href'], $service) !== FALSE) {
          $icon = $service;
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
   * @param array $options
   *   An array containing options.
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
  public static function prepareSectionLink($entity, array $options = []) {

    $map = [
      'text' => [
        'field_lede',
        'field_service_body',
        'field_guide_page_lede',
        'field_service_lede',
        'field_topic_lede',
        'field_topic_lede',
        'field_sub_title',
      ],
      'icon' => [
        'field_icon_term',
        'field_topic_ref_icon',
      ],
      'links' => [
        'field_topic_content_cards',
        'field_service_ref_actions_2',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $index = &drupal_static(__FUNCTION__);
    $index++;

    $icon = '';

    if ($fields['icon']) {
      $icon = [
        'icon' => Helper::getIconPath($entity->$fields['icon']->referencedEntities()[0]->field_sprite_name->value),
        'small' => 'true',
      ];
    }

    if ($fields['links']) {
      foreach ($entity->$fields['links'] as $link) {
        $linkEntity = $link->entity;

        if (!empty($linkEntity->field_content_card_link_cards)) {
          foreach ($linkEntity->field_content_card_link_cards as $linkItem) {
            $links[] = Helper::separatedLink($linkItem);
          }
        }
        else {
          $links = Helper::separatedLinks($entity, $fields['links']);
        }
      }

      if (in_array($entity->getType(), $options['use4TopLinks'])) {
        // Only show top 4 links.
        $links = array_slice($links, 0, 4);
      }
    }

    $seeAll = [
      'href' => $entity->toURL()->toString(),
      'text' => 'more',
    ];

    // Different options for topic_page, org_page, and service_page.
    return [
      'id' => 'section_link_' . $index,
      'catIcon' => in_array($entity->getType(), isset($options['useIcon']) ? $options['useIcon'] : []) ? $icon : '',
      'title' => [
        'href' => $entity->toURL()->toString(),
        'text' => $entity->getTitle(),
      ],
      'description' => !empty($entity->$fields['text']->value) ? Helper::fieldValue($entity, $fields['text']) : '',
      'type' => in_array($entity->getType(), isset($options['useCallout']) ? $options['useCallout'] : []) ? 'callout' : '',
      'links' => in_array($entity->getType(), $options['noCardLinks']) ? '' : $links,
      'seeAll' => in_array($entity->getType(), isset($options['noSeeAll']) ? $options['noSeeAll'] : []) ? '' : $seeAll,
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

      // Creates a map of fields that are on the entity.
      $map = [
        'details' => ['field_caption'],
        'label' => ['field_label'],
        'hours' => ['field_ref_hours', 'field_hours'],
        'value' => [
          'field_address_address',
          'field_phone',
          'field_fax',
          'field_media_contact_phone',
          'field_person_phone',
        ],
        'link' => [
          'field_link_single',
          'field_email',
          'field_media_contact_email',
          'field_person_email',
        ],
      ];

      // Determines which fieldnames to use from the map.
      $fields = Helper::getMappedFields($entity, $map);

      if (array_key_exists('details', $fields) && Helper::isFieldPopulated($entity, $fields['details'])) {
        $item['details'] = Helper::fieldFullView($entity, $fields['details']);
      }

      if (array_key_exists('label', $fields) && Helper::isFieldPopulated($entity, $fields['label'])) {
        $item['label'] = Helper::fieldFullView($entity, $fields['label']);
      }

      if (array_key_exists('hours', $fields) && Helper::isFieldPopulated($entity, $fields['hours'])) {
        $item['hours'] = Helper::fieldFullView($entity, $fields['hours']);
      }

      if ($type == 'address') {
        $address = Helper::formatAddress($entity->$fields['value'], $options);
        $item['value'] = $address;
        $item['link'] = 'https://maps.google.com/?q=' . urlencode($address);
        $item['info'] = t('Get directions to ') . $address;

        // Respect first address provided if present.
        if (!$contactInfo['address']) {
          $contactInfo['address'] = $address;
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
        // Checks for email link fields.
        $bundle = $entity->getType();
        $bundles_using_email_fields = [
          'online_email',
          'media_contact',
        ];
        if (in_array($bundle, $bundles_using_email_fields)) {
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
      'title' => ['field_display_title', 'field_media_contact_name'],
    ];

    // Determines which fieldnames to use from the map.
    $referenced_fields = Helper::getMappedFields($entity, $reference_map);

    $groups = [];

    if (!empty($referenced_fields)) {
      foreach ($referenced_fields as $id => $field) {
        if (Helper::isFieldPopulated($entity, $field)) {
          $items = Helper::getReferencedEntitiesFromField($entity, $field);
          $groups[] = Molecules::prepareContactGroup($items, $options + ['type' => $id], $contactInfo);
        }
      }
    }
    else {
      $groups[] = Molecules::prepareContactGroup([0 => $entity], $options + ['type' => 'phone'], $contactInfo);
      $groups[] = Molecules::prepareContactGroup([0 => $entity], $options + ['type' => 'online'], $contactInfo);
    }

    $fields = Helper::getMappedFields($entity, $map);

    $display_title = $options['display_title'];
    $only_address = $options['onlyAddress'];
    $link_title = isset($options['link_title']) ? $options['link_title'] : TRUE;

    if (isset($fields['title']) && Helper::isFieldPopulated($entity, $fields['title']) && $display_title != FALSE) {
      if ($link_title != FALSE) {
        $title = [
          'href' => $entity->toURL()->toString(),
          'text' => $entity->$fields['title']->value,
          'chevron' => FALSE,
        ];
      }
      else {
        $title = [
          'text' => $entity->$fields['title']->value,
        ];
      }
    }
    elseif ($display_title != FALSE) {
      if ($link_title != FALSE) {
        $title = [
          'href' => $entity->toURL()->toString(),
          'text' => $entity->getTitle(),
          'chevron' => FALSE,
        ];
      }
      else {
        $title = [
          'text' => $entity->getTitle(),
        ];
      }
    }

    // If set, only display address.
    if ($only_address != FALSE && isset($groups[0])) {
      $groups = array_slice($groups, 0, 1);
    }

    return [
      'schemaSd' => [
        'property' => 'containedInPlace',
        'type' => 'CivicStructure',
      ],
      'schemaContactInfo' => $contactInfo,
      'accordion' => isset($options['accordion']) ? $options['accordion'] : FALSE,
      'isExpanded' => isset($options['isExpanded']) ? $options['isExpanded'] : TRUE,
      // TODO: Needs validation if empty or not.
      'subTitle' => $title,
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
   *   An array of options including heading data structure.
   *   options = [
   *     heading => [
   *       type = compHeading || sidebarHeading,
   *       title = t('Key Actions'),
   *       sub = FALSE,
   *     ],
   *   ].
   *
   * @see @molecules/callout-alert.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareKeyActions($entity, $field = '', array $options = []) {
    $key_actions = [];

    $map = [
      'field' => [$field],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Roll up our Key Action links.
    $key_actions['links'] = Helper::separatedLinks($entity, $fields['field']);

    // Populate heading data structure based on options passed.
    if (array_key_exists('heading', $options)) {
      $key_actions[$options['heading']['type']] = $options['heading'];
    }

    return $key_actions;
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
          'lat_lng' => ['field_geofield'],
          'address' => ['field_address_address'],
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
            'address' => Helper::formatAddress($addressEntity->$address_fields['address']),
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
   * @param string $address
   *   A string address if exists.
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
  public static function prepareGoogleMap(array $entities, $address = '') {
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
          'address' => !empty($address) ? $address : '',
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
      'icons' => ['field_location_icons', 'field_form_payment_options'],
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
      'text' => Helper::fieldValue($entity, $fields['field']),
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
  public static function prepareDownloadLink($entity, array $options = []) {
    $itsAFile = FALSE;
    $itsALink = FALSE;
    $icon = '';
    $title = '';
    $href = '';

    if ($entity instanceof File || $entity instanceof Media) {
      $file = ($entity instanceof File) ? $entity : File::load($entity->field_upload_file->target_id);
      $itsAFile = TRUE;
      // Get file info.
      $bytes = $file->getSize();
      $readable_size = format_size($bytes);
      $title = !empty($entity->field_title->value) ? $entity->field_title->value : $file->getFilename();
      $file_info = new \SplFileInfo($file->getFilename());
      $file_extension = $file_info->getExtension();
      $href = file_create_url($file->getFileUri());
      $icon = Helper::getIconPath(strtolower('doc-' . $file_extension));
    }

    if ($entity instanceof LinkItem) {
      $itsALink = TRUE;
      $title = $entity->title;
      $icon = Helper::getIconPath('laptop');
      $href = $entity->getUrl()->toString();
    }

    return [
      'downloadLink' => [
        'iconSize' => '',
        'icon' => $icon,
        'decorativeLink' => [
          'text' => $title,
          'href' => $href,
          'info' => '',
          'propery' => '',
        ],
        'size' => ($itsAFile) ? strtoupper($readable_size) : '',
        'format' => ($itsAFile) ? strtoupper($file_extension) : '',
      ],
    ];
  }

  /**
   * Returns the data structure necessary for sticky nav.
   *
   * @param array $navLinksText
   *   Array of strings (i.e. "What you need") used to generate anchor links.
   *
   * @see @molecules/sticky-nav.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareStickyNav(array $navLinksText) {
    $anchorLinks = array_map(function ($text) {
      return [
        "href" => Helper::createIdTitle($text),
        "text" => $text,
        "info" => "",
      ];
    }, $navLinksText);

    return [
      'anchorLinks' => $anchorLinks,
    ];
  }

  /**
   * Returns the variables structure required to render event teaser.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param object $fields
   *   Field objects for contact, date, time, location and lede.
   * @param array $options
   *   headerDate option to set the display to only display date and time.
   *
   * @see @molecules/event-teaser.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareEventTeaser($entity, $fields, array $options = []) {
    $location = '';
    if (isset($fields['contact']) && !empty($entity->$fields['contact']->entity)) {
      $contentUsEntity = $entity->$fields['contact']->entity;
      $location = Helper::fieldValue($contentUsEntity->field_ref_address->entity, 'field_address_text');
    }

    $date = !empty($entity->$fields['date']->value) ? strtotime($entity->$fields['date']->value) : '';

    $dateTime = new \DateTime($entity->$fields['date']->value, new \DateTimeZone('UTC'));
    $timezone = 'America/New_York';
    $dateTime->setTimeZone(new \DateTimeZone($timezone));
    $time = $dateTime->format('g:ia');

    if (isset($options['headerDate'])) {
      return [
        'date' => [
          'summary' => $dateTime->format('l, F d, Y'),
        ],
        'time' => !empty($entity->$fields['time']->value) ? Helper::fieldValue($entity, $fields['time']) : $time,
      ];
    }
    else {
      return [
        'title' => [
          'href' => $entity->toURL()->toString(),
          'text' => Helper::fieldValue($entity, 'title'),
          'info' => '',
          'property' => '',
        ],
        'location' => $location,
        'date' => [
          'summary' => $dateTime->format('F d, Y'),
          'startMonth' => $dateTime->format('M'),
          'startDay' => $dateTime->format('d'),
          'endMonth' => '',
          'endDay' => '',
        ],
        'time' => !empty($entity->$fields['time']->value) ? Helper::fieldValue($entity, $fields['time']) : $time,
        'description' => !empty($entity->$fields['lede']->value) ? Helper::fieldValue($entity, $fields['lede']) : '',
      ];
    }
  }

  /**
   * Returns the variables structure required to render pressStatus.
   *
   * @param object $entity
   *   The object that contains the fields.
   *
   * @see @molecules/press-status.twig
   *
   * @return array
   *   Return a structured array:
   */
  public static function preparePressStatus($entity) {
    $names = [];
    $immediateRelease = FALSE;

    $map = [
      'date' => ['field_news_date'],
      'signees' => ['field_news_signees'],
      'news_type' => ['field_news_type'],
      'speaker' => ['field_news_speaker'],
      'presented_to' => ['field_news_presented'],
    ];

    // Determines which field names to use from the map.
    $field = Helper::getMappedFields($entity, $map);

    if (Helper::fieldValue($entity, $field['news_type']) == 'press_release') {
      $immediateRelease = TRUE;
    }

    if (!empty($entity->$field['signees']->entity)) {
      foreach ($entity->$field['signees'] as $paragraph) {
        $par_entity = $paragraph->entity;
        $org_fields = [
          'link' => ['field_state_org_ref_org'],
          'title' => ['field_state_org_name', 'field_external_org_name'],
          'lede' => ['field_state_org_description', 'field_external_org_description'],
          'image' => ['field_state_org_photo', 'field_external_org_photo'],
        ];

        // Determines which field names to use from the map.
        $ref_field = Helper::getMappedFields($par_entity, $org_fields);

        if (Helper::fieldValue($entity, $field['news_type']) == 'speech') {
          if (Helper::isFieldPopulated($entity, $field['speaker'])) {
            $names[]['text'] = Helper::fieldValue($entity, $field['speaker']);
          }
          if (Helper::isFieldPopulated($entity, $field['presented_to'])) {
            $names[]['text'] = Helper::fieldValue($entity, $field['presented_to']);
          }
        }

        if ($par_entity->getParagraphType()->id == 'state_organization') {
          if (Helper::isFieldPopulated($par_entity, $ref_field['title'])) {
            $names[]['text'] = Helper::fieldValue($par_entity, $ref_field['title']);
          }
          else {
            $names[]['text'] = $par_entity->field_state_org_ref_org->entity->getTitle();
          }
        }

        if ($par_entity->getParagraphType()->id == 'external_organization') {
          if (Helper::isFieldPopulated($par_entity, $ref_field['title'])) {
            $names[]['text'] = Helper::fieldValue($par_entity, $ref_field['title']);
          }
        }
      }
    }

    return [
      'title' => ($immediateRelease) ? t("For immediate release:") : '',
      'date' => Helper::fieldFullView($entity, $field['date']),
      'names' => $names,
    ];
  }

}
