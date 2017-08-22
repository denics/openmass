<?php

namespace Drupal\mayflower\Prepare;

use Drupal\mayflower\Helper;
use Drupal\Component\Utility\UrlHelper;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides variable structure for mayflower organisms using prepare functions.
 */
class Organisms {

  /**
   * Returns the variables structure required to render an action finder.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param array $options
   *   The object that contains static data and other options.
   * @param array $map
   *   The map of fields for this entity used to populate the component:
   *    [
   *      'bgWide' => [field_name],
   *      'bgNarrow' => [field_name],
   *      'featured_actions' => ['field_name'],
   *      'all_actions' => ['field_name'],
   *      'see_all' => ['field_name'],
   *    ].
   *
   * @see @organisms/by-author/action-finder.twig
   *
   * @return array
   *   Returns an array of items that contain:
   *   "actionFinder": [
   *      "bgWide":"/assets/images/placeholder/1600x600-lighthouse-blur.jpg",
   *      "bgNarrow":"/assets/images/placeholder/800x800.png",
   *      "title": "What Would You Like to Do?",
   *      "featuredHeading":"Featured:",
   *      "generalHeading":"All Actions & Guides:",
   *      "id": "UUID01234...",
   *      "seeAll": [
   *        "type": "external",
   *        "href": "http://www.google.com",
   *        "text": "See all EOHHS’s programs and services on
   *                 classic Mass.gov",
   *        "info": ""
   *      ],
   *      "featuredLinks": [[
   *        "image": "/assets/images/placeholder/130x160.png",
   *        "text": "Getting Outdoors",
   *        "type": "",
   *        "href": "#"
   *      ], ... ],
   *      "links": [[
   *        "image": "",
   *        "text": "Find a State Park",
   *        "type": "",
   *        "href": "#"
   *      ], ... ]
   *    ]
   */
  public static function prepareActionFinder($entity, array $options = [], array $map = []) {
    $map = empty($map) ? [
      'bgWide' => ['field_action_set__bg_wide'],
      'bgNarrow' => ['field_action_set__bg_narrow'],
      'featured_actions' => ['field_ref_actions_3', 'field_service_ref_actions_2'],
      'all_actions' => ['field_ref_actions_6', 'field_service_ref_actions'],
      'see_all' => ['field_link'],
    ] : $map;

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $featured_heading = '';
    $featured_links = '';
    if (array_key_exists('featured_actions', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['featured_actions'])) {
        $featured_heading = array_key_exists('featuredHeading', $options) ? $options['featuredHeading'] : "Featured: ";
        $featured_links = Helper::createIllustratedOrCalloutLinks($entity, $fields['featured_actions']);
      }
    }

    $all_heading = '';
    $links = '';
    if (array_key_exists('all_actions', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['all_actions'])) {
        $all_heading = array_key_exists('generalHeading', $options) ? $options['generalHeading'] : "All: ";
        $links = Helper::createIllustratedOrCalloutLinks($entity, $fields['all_actions']);
      }
    }

    // Build see all link.
    // @todo Consider making this its own prepare function
    $see_all = NULL;
    if (array_key_exists('see_all', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['see_all'])) {
        // @todo update mayflower_separated_links so we don't need [0]
        $see_all = Helper::separatedLinks($entity, $fields['see_all'])[0];
      }
    }

    // Set actionFinder id to entity uuid.
    $id = 'UUID' . $entity->uuid();

    // Get desktop image, if it exists.
    $desktop_image = '';
    if (array_key_exists('bgWide', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['bgWide'])) {
        $desktop_image = Helper::getFieldImageUrl($entity, 'action_finder_mobile', $fields['bgWide']);
      }
    }

    // Use the desktop image by default if there is no mobile image.
    $mobile_image = $desktop_image;

    // Get mobile image if exists use mobile image and mobile image style.
    if (array_key_exists('bgNarrow', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['bgNarrow'])) {
        $mobile_image = Helper::getFieldImageUrl($entity, 'action_finder_mobile', $fields['bgNarrow']);
      }
    }

    // Build actionFinder data array.
    return [
      'actionFinder' => [
        'title' => $options["title"],
        'featuredHeading' => $featured_heading,
        'generalHeading' => $all_heading,
        'id' => $id,
        'bgWide' => $desktop_image,
        'bgNarrow' => $mobile_image,
        'seeAll' => $see_all,
        'featuredLinks' => $featured_links,
        'links' => $links,
      ],
    ];
  }

  /**
   * Returns the variables structure required to render a page header.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param array $options
   *   The object that contains static data, widgets, and optional content.
   *
   * @see @organisms/by-template/page-header.twig
   *
   * @return array
   *   Returns an array of items.
   *    "pageHeader": [
   *      "title": "Executive Office of Health and Human Services",
   *      "titleNote": "(EOHHS)",
   *      "subTitle": "",
   *      "rteElements": "",
   *      "headerTags": ""
   *      "optionalContents": [[
   *         "path": "[path/to/pattern]",
   *         "data": []
   *       ], ... ],
   *      "divider": false / true,
   *      "widgets": [[
   *         "path": "[path/to/pattern]",
   *         "data": []
   *       ], ... ]
   *    ]
   */
  public static function preparePageHeader($entity, array $options) {
    // Create the map of all possible field names to use.
    $map = [
      'title' => ['title'],
      'titleNote' => ['field_title_sub_text'],
      'subTitle' => [
        'field_sub_title',
        'field_how_to_lede',
        'field_service_detail_lede',
        'field_location_details_lede',
        'field_form_lede',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Create the actionHeader data structure.
    $pageHeader = [
      'title' => isset($entity->$fields['title']) ? $entity->$fields['title']->value : '',
      'titleNote' => isset($fields['titleNote']) ? $entity->$fields['titleNote']->value : '',
      'subTitle' => isset($fields['subTitle']) ? $entity->$fields['subTitle']->value : '',
      'divider' => array_key_exists('divider', $options) ? $options['divider'] : FALSE,
      'optionalContents' => array_key_exists('optionalContents', $options) ? $options['optionalContents'] : NULL,
      'widgets' => array_key_exists('widgets', $options) ? $options['widgets'] : NULL,
      'category' => array_key_exists('category', $options) ? $options['category'] : NULL,
      'headerTags' => array_key_exists('headerTags', $options) ? $options['headerTags'] : NULL,
      'publishState' => array_key_exists('publishState', $options) ? $options['publishState'] : NULL,
    ];

    return $pageHeader;
  }

  /**
   * Returns the variables structure required to render an illustrated header.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param object $options
   *   The object that contains static data and other options.
   *
   * @see @organisms/by-template/illustrated-header.twig
   *
   * @return array
   *   Returns a structured array of header items.
   */
  public static function prepareIllustratedHeader($entity, $options) {
    // Create the map of all possible field names to use.
    $map = [
      'title' => ['title'],
      'lede' => ['field_guide_page_lede'],
      'bgWide' => ['field_guide_page_bg_wide'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $optionalContents = [];
    $richTextElements = [];

    if (Helper::isFieldPopulated($entity, $fields['lede'])) {
      $richTextElements[] = Atoms::prepareParagraph($entity, ['field' => $fields['lede']]);
    }

    $optionalContents[] = Helper::prepareRichTextElements($richTextElements);

    // Create the illustratedHeader data structure.
    return [
      'bgTitle' => $entity->$fields['title']->value,
      'bgImage' => Helper::getFieldImageUrl($entity, 'headerbg_800x450', $fields['bgWide']),
      'category' => "Guide",
      'pageHeader' => [
        'title' => $entity->$fields['title']->value,
        'subTitle' => '',
        'optionalContents' => $optionalContents,
      ],
      'headerTags' => "",
    ];
  }

  /**
   * Returns the variables structure required to render a sidebarContact.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param array $options
   *   An array of options for sidebar contact.
   *
   * @see @organisms/by-author/sidebar-contact.twig
   *
   * @return array
   *   Returns an array of items.
   *   'sidebarContact': array(
   *      'coloredHeading': array(
   *        'text': string / required,
   *        'color': string / optional
   *      ),
   *      'items': array(
   *         contactUs see @molecules/contact-us
   *      ).
   */
  public static function prepareSidebarContact($entity, array $options = []) {
    $items = [];
    $sidebarContact = [];

    // Create the map of all possible field names to use.
    $map = [
      'items' => [
        'field_ref_contact_info',
        'field_guide_ref_contacts_3',
        'field_event_contact_general',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $contactUs = [];

    $ref_items = Helper::getReferencedEntitiesFromField($entity, $fields['items']);

    foreach ($ref_items as $item) {
      $item_options = ['display_title' => TRUE, 'sidebar' => TRUE];
      $items[] = ['contactUs' => Molecules::prepareContactUs($item, $item_options)];
    }

    if (!empty($items)) {
      $heading = Helper::buildHeading($options['heading']);
      $sidebarContact = array_merge($heading, ['items' => $items]);
    }

    return $sidebarContact;
  }

  /**
   * Returns the variables structure required to render a contactList.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param array $options
   *   An array of options for sidebar contact.
   *
   * @see @organisms/by-author/contact-list.twig
   *
   * @return array
   *   Returns an array of items.
   *   'contactList': array(
   *      'compHeading': array(
   *        'text': string / required,
   *        'color': string / optional,
   *        'id': string / optional,
   *        'sub': boolean / required if TRUE,
   *        'centered': boolean / required if TRUE,
   *      ),
   *      'contacts': array(
   *         contactUs see @molecules/contact-us
   *      ).
   */
  public static function prepareContactList($entity, array $options = []) {
    $contacts = [];

    // Create the map of all possible field names to use.
    $map = [
      'contacts' => [
        'field_how_to_contacts_3',
        'field_news_media_contac',
        'field_press_release_media_contac',
        'field_event_contact_general',
        'field_executive_order_contact',
        'field_decision_ref_contact',
        'field_advisory_ref_contact',
        'field_form_ref_contacts_3',
        'field_regulation_contact',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $ref_contacts = Helper::getReferencedEntitiesFromField($entity, $fields['contacts']);

    $options['groups']['display_title'] = TRUE;
    $options['groups']['link_title'] = FALSE;

    foreach ($ref_contacts as $contact) {
      $contacts[] = Molecules::prepareContactUs($contact, $options['groups']);
    }

    $contactList = [];
    if (!empty($contacts)) {
      // Build a sidebar, comp, or colored heading based on heading type option.
      $options['heading']['title'] = isset($options['heading']['title']) ? $options['heading']['title'] : t('Contacts');
      $heading = Helper::buildHeading($options['heading']);

      $contactList = array_merge($heading, ['contacts' => $contacts]);
    }

    // Create the contactList data structure.
    return $contactList;
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
   * @see @organisms/by-author/key-actions.twig
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

    $links = Helper::createIllustratedOrCalloutLinks($entity, $fields['field']);

    $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];

    return array_merge($heading, ['links' => $links]);
  }

  /**
   * Returns the variables structure required to render eventListing.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param string $field
   *   The field name.
   * @param array $options
   *   An array of options.
   *
   * @see @organisms/by-author/event-listing.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareEventListing($entity, $field = '', array $options = []) {
    $events = [];
    $map = [
      'entity_ref' => [$field],
    ];
    // Determines which fieldnames to use from the map.
    $ref_field = Helper::getMappedFields($entity, $map);

    // Create the map of event fields.
    $eventFields = [
      'date' => ['field_event_date'],
      'time' => ['field_event_time'],
      'lede' => ['field_event_lede'],
      'contact' => ['field_event_ref_contact'],
    ];

    foreach ($entity->$ref_field['entity_ref'] as $event) {
      $eventEntity = $event->entity;
      if (!empty($eventEntity)) {
        // Determines which field names to use from event fields.
        $fields = Helper::getMappedFields($eventEntity, $eventFields);
        $events[] = Molecules::prepareEventTeaser($eventEntity, $fields, $options);
      }
      else {
        // Load event from Link field.
        if (!empty($event->uri)) {
          $entity = Helper::entityFromUri($event->uri);
          $fields = Helper::getMappedFields($entity, $eventFields);
          $events[] = Molecules::prepareEventTeaser($entity, $fields, $options);
        }
      }
    }

    $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];
    return array_merge($heading, ['events' => $events]);
  }

  /**
   * Returns the variables structure required to render link list.
   *
   * @param object $entity
   *   An array of objects that contains the fields.
   * @param string $field
   *   The link / entity reference field name.
   * @param array $options
   *   An array of options for sidebar contact.
   *
   * @see @organisms/by-author/link-list.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    "linkList" : [
   *      "title": "Related Organizations on pilot.mass.gov",
   *      "links" : [[
   *        "url":"#",
   *        "text":"Executive Office of Elder Affairs"
   *      ],... ]
   *    ]
   */
  public static function prepareLinkList($entity, $field, array $options = []) {

    $linkList = [];

    // Build description, if option is set.
    if (isset($options['description'])) {
      $description = [
        'rteElements' => [
          [
            'path' => '@atoms/11-text/paragraph.twig',
            'data' => [
              'paragraph' => [
                'text' => $options['description']['text'],
              ],
            ],
          ],
        ],
      ];
    }

    // Roll up the link list.
    $links = Helper::separatedLinks($entity, $field, $options);

    if (!empty($links)) {
      // Build either sidebar or comp heading based on heading type option.
      $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];
      $linkList = array_merge($heading, ['links' => $links]);
    }
    $linkList['description'] = isset($options['description']) ? $description : '';
    $linkList['stacked'] = isset($options['stacked']) ? $options['stacked'] : '';

    return $linkList;
  }

  /**
   * Returns the variables structure required to render press list.
   *
   * @param object $entity
   *   An array of objects that contains the fields.
   * @param string $field
   *   The link / entity reference field name.
   * @param array $options
   *   An array of options for sidebar contact.
   * @param array $secondaryEntities
   *   An array of secondary items.
   *
   * @see @organisms/by-author/press-listing.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function preparePressListing($entity, $field, array $options = [], array $secondaryEntities = []) {
    $items = [];
    $secondaryItems = [];
    $pressList = [];
    $i = 0;

    // Roll up entities in field.
    $entities = $entity->get($field);

    foreach ($entities as $link) {
      // On an internal link item, load the referenced node title.
      if (strpos($link->getValue()['uri'], 'entity:node') !== FALSE) {
        $options['url'] = $link->getUrl();
        $options['text'] = $link->getValue()['title'];
        if (method_exists($options['url'], 'getRouteParameters') && $options['url']->isRouted() == TRUE) {
          $params = $options['url']->getRouteParameters();
          $entity_type = key($params);
          $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
          if (!empty($entity) && $entity instanceof ContentEntityInterface) {
            $items[] = Molecules::preparePressTeaser($entity, $options);
          }
        }
      }
    }

    foreach ($entities as $entity) {
      if (!empty($entity->entity)) {
        $items[] = Molecules::preparePressTeaser($entity->entity, $options);
      }
    }

    foreach ($secondaryEntities as $index => $entity) {
      if (isset($options['numOfSecondaryItems']) && ++$i > (int) $options['numOfSecondaryItems']) {
        break;
      }
      $secondaryItems[] = Molecules::preparePressTeaser($entity, $options);
    }

    if (!empty($items) || !empty($secondaryItems)) {
      $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];
      $pressList = array_merge($heading, ['items' => $items, 'secondaryItems' => $secondaryItems]);
    }

    return $pressList;
  }

  /**
   * Returns the variables structure required to render a page banner.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param object $options
   *   The object that contains static data and other options.
   *
   * @see @organisms/by-template/page-banner.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [
   *      "bgWide":"/assets/images/placeholder/1600x400.png"
   *      "bgNarrow":"/assets/images/placeholder/800x400.png",
   *      "size": "large",
   *      "icon": null,
   *      "title": "Executive Office of Health and Human Services",
   *      "titleSubText": "(EOHHS)"
   *    ]
   */
  public static function preparePageBanner($entity, $options = NULL) {
    $pageBanner = [];

    // Create the map of all possible field names to use.
    $map = [
      'title' => ['title'],
      'title_sub_text' => ['field_title_sub_text'],
      'bg_wide' => [
        'field_bg_wide',
        'field_service_bg_wide',
        'field_topic_bg_wide',
      ],
      'bg_narrow' => [
        'field_bg_narrow',
        'field_service_bg_narrow',
        'field_topic_bg_narrow',
      ],
      'description' => ['field_lede', 'field_topic_lede'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // @TODO consider passing the image style in as an option.
    // Use action_banner_* as default pageBanner image styles.
    $image_style_wide = 'action_banner_large';
    $image_style_narrow = 'action_banner_small';

    // Get pageBanner size, use as flag to determine image style.
    $pageBanner['size'] = array_key_exists('size', $options) ? $options['size'] : 'large';

    // Use appropriate image style for various pageBanner sizes.
    if ($pageBanner['size'] === 'columns') {
      // Use original image style for hotfix to avoid config/DB changes.
      $image_style_wide = 'Hero820x460_no_blur';
      $image_style_narrow = 'Hero800x400_no_blur';
      // @TODO fix the hero820 image style so that it is not blurred.
      // $image_style_wide = 'hero820x460';
      // $image_style_narrow = 'hero800x400';
    }
    elseif ($pageBanner['size'] === 'hero1600x400') {
      $image_style_wide = 'Hero1600x400';
      $image_style_narrow = 'Hero800x400_no_blur';
    }

    // Use helper function to get the image url of a given image style.
    $pageBanner['bgWide'] = Helper::getFieldImageUrl($entity, $image_style_wide, $fields['bg_wide']);
    $pageBanner['bgNarrow'] = Helper::getFieldImageUrl($entity, $image_style_narrow, $fields['bg_narrow']);

    if ($options['type'] == 'section landing') {
      // Manually specified since we have potentially 4 image fields on topic_page.
      $pageBanner['bgWide'] = Helper::getFieldImageUrl($entity, $image_style_wide, 'field_topic_section_bg_wide');
      if (Helper::isFieldPopulated($entity, 'field_topic_section_bg_narrow')) {
        $pageBanner['bgNarrow'] = Helper::getFieldImageUrl($entity, $image_style_narrow, 'field_topic_section_bg_narrow');
      }
    }

    // @todo determine how to handle options vs field value (check existence, order of importance, etc.)
    $pageBanner['icon'] = $options['icon'];
    $pageBanner['color'] = array_key_exists('color', $options) ? $options['color'] : '';

    $pageBanner['title'] = $entity->$fields['title']->value;

    $title_sub_text = '';
    if (array_key_exists('title_sub_text', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['title_sub_text'])) {
        $title_sub_text = $entity->$fields['title_sub_text']->value;
      }
    }
    $pageBanner['titleSubText'] = $title_sub_text;

    $description = '';
    if (array_key_exists('description', $fields)) {
      if (Helper::isFieldPopulated($entity, $fields['description'])) {
        $description = $entity->$fields['description']->value;
      }
    }

    if ($options['type'] != 'section landing') {
      $pageBanner['description'] = $description;
    }

    return $pageBanner;
  }

  /**
   * Returns the variables structure required to render section Three Up.
   *
   * @param object $entities
   *   The object that contains the fields.
   * @param array $options
   *   An array containing options.
   * @param array $field_map
   *   An optional array of fields.
   *
   * @see @organsms/by-author/sections-three-up
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareSectionThreeUp($entities, array $options = [], array $field_map = NULL) {
    $sections = [];
    $fields = [];

    if ($field_map) {
      $fields = Helper::getMappedFields($entities, $field_map);
    }

    // Load up our entity if internal.
    if ($fields['topic_cards']) {
      foreach ($entities->$fields['topic_cards'] as $card) {
        $url = $card->getUrl();
        $desc = '';
        $seeAll = '';
        // Load up our entity if internal.
        if ($url->isExternal() == FALSE && method_exists($url, 'getRouteParameters')) {
          $params = $url->getRouteParameters();
          $entity_type = key($params);
          $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
          if (!empty($entity)) {
            $cards[] = Molecules::prepareSectionLink($entity, $options);
          }
        }
        else {
          $cards[] = [
            'title' => Helper::separatedLink($card),
            'description' => $desc,
            'seeAll' => $seeAll,
          ];
        }
        $sections = $cards;
      }
    }
    else {
      foreach ($entities as $entity) {
        $sections[] = Molecules::prepareSectionLink($entity->entity, $options);
      }
    }

    $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];
    return array_merge($heading, ['sections' => $sections]);
  }

  /**
   * Returns the variables structure required for QuickActions.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   An array containing options.
   *
   * @see @organsms/by-author/quick-actions.twig
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareQuickActions($entity, array $options = []) {
    $quickActions = [];
    $links = [];

    // Create the map of all possible field names to use.
    $map = [
      'links' => ['field_links', 'field_how_to_links_5'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Get related locations.
    foreach ($entity->$fields['links'] as $link) {
      $links[] = Helper::separatedLink($link);
    }

    if (!empty($links)) {
      // Build either sidebar or comp heading based on heading type option.
      $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];
      $quickActions = array_merge($heading, ['links' => $links]);
    }

    return $quickActions;
  }

  /**
   * Returns the variables structure required for SuggestedPages.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   An array of options for title, view, imageStyle.
   *
   * @see @organsms/by-author/suggested-pages.twig
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareSuggestedPages($entity, array $options = []) {
    $pages = [];

    // Create the map of all possible field names to use.
    $map = [
      'items' => ['field_related_locations', 'field_guide_page_related_guides'],
    ];
    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    if ($entity->$fields['items']->isEmpty() || !method_exists($entity, 'hasField')) {
      return [];
    }

    // Get related locations.
    foreach ($entity->$fields['items'] as $item) {
      $ref_entity = $item->entity;

      if (!method_exists($ref_entity, 'hasField')) {
        return [];
      }

      // Creates a map of fields that are on the entitiy.
      $ref_map = [
        'image' => ['field_bg_wide', 'field_guide_page_bg_wide'],
      ];

      // Determines which field names to use from the map.
      $field = Helper::getMappedFields($ref_entity, $ref_map);

      $pages[] = [
        'image' => Helper::getFieldImageUrl($ref_entity, isset($options['style']) ? $options['style'] : '', $field['image']),
        'altTag' => $ref_entity->alt,
        'link' => [
          'type' => UrlHelper::isExternal($ref_entity->toURL()->toString()) ? 'external' : 'internal',
          'href' => $ref_entity->toURL()->toString(),
          'text' => $ref_entity->getTitle(),
        ],
      ];
    }

    return [
      'view' => isset($options['view']) ? $options['view'] : '',
      'title' => isset($options['title']) ? $options['title'] : '',
      'buttonMinor' => [
        'href' => '',
        'text' => '',
      ],
      'pages' => $pages,
    ];
  }

  /**
   * Returns the variables structure required to render an jump links.
   *
   * @param array $sections
   *   The sections that contains the necessary fields.
   * @param array $options
   *   The array that contains static data and other options.
   *
   * @see @organisms/by-template/jump-links.twig
   *
   * @return array
   *   Returns a structured array of jump links.
   */
  public static function prepareJumpLinks(array $sections, array $options) {
    // Create the links data structure.
    foreach ($sections as $section) {
      if (!is_object($section['title']) && !empty($section['title']['compHeading']['title'])) {
        $title = $section['title']['compHeading']['title'];
      }
      else {
        $title = $section['title'];
      }

      $links[] = [
        'text' => $title,
        'href' => $section['id'],
      ];
    }

    return [
      'title' => $options['title'],
      'links' => $links,
    ];
  }

  /**
   * Returns the variables structure required for ActionDetails.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param object $options
   *   The object that contains static data and other options.
   *
   * @see @organsms/by-author/action-details
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareActionDetails($entity, $options = NULL) {
    $sections = [];
    $locationType = '';

    if (!empty($options['locationType'])) {
      $locationType = $options['locationType'];
    }

    // Create the map of all possible field names to use.
    $map = [
      'overview' => ['field_overview'],
      'primary_location' => ['field_ref_contact_info_1'],
      'contacts' => ['field_ref_contact_info'],
      'parking' => ['field_parking'],
      'markers' => ['field_maps'],
      'activities' => ['field_location_activity_detail'],
      'facilities' => ['field_location_facilities'],
      'accessibility' => ['field_accessibility'],
      'restrictions' => ['field_restrictions'],
      'services' => ['field_services'],
      'information' => ['field_location_more_information'],
      'all_activities' => ['field_location_all_activities'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Overview section.
    if (Helper::isFieldPopulated($entity, $fields['overview'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_overview']);
    }

    // Hours section.
    if (Helper::isFieldPopulated($entity, $fields['primary_location'])) {

      $contact_entities[] = Helper::getReferencedEntitiesFromField($entity, $fields['primary_location']);
      foreach ($contact_entities as $contact) {
        $contact_entity = $contact[0];
        // Don't know why this map isn't working.
        // $contact_fields = Helper::getMappedFields($contact_entity, $contact_map)
        $sections[] = Helper::buildHours($contact_entity->field_ref_hours, 'Hours');
      }
    }
    if (Helper::isFieldPopulated($entity, $fields['contacts'])) {
      $contact_refs[] = Helper::getReferencedEntitiesFromField($entity, $fields['contacts']);
      foreach ($contact_refs as $contact) {
        $contact_ref = $contact[0];
        // Don't know why this map isn't working.
        // $contact_fields = Helper::getMappedFields($contact_entity, $contact_map)
        $sections[] = Helper::buildHours($contact_ref->field_ref_hours, '');
      }
    }

    // Parking section.
    if (Helper::isFieldPopulated($entity, $fields['parking'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_parking']);
    }

    // Activities section.
    if (Helper::isFieldPopulated($entity, $fields['activities']) && $locationType == 'park') {
      $title = t('Activities');

      $sections[] = [
        'title' => $title,
        'into' => '',
        'id' => Helper::createIdTitle($title),
        'path' => '@organisms/by-author/image-promos.twig',
        'data' => [
          'imagePromos' => Organisms::prepareImagePromos($entity->$fields['activities']),
        ],
      ];
    }

    if (Helper::isFieldPopulated($entity, $fields['all_activities']) && $locationType == 'park') {
      // Roll up taxo terms into unordered list.
      $activities = '<ul>';
      foreach ($entity->$fields['all_activities'] as $activity) {
        $activities .= '<li>' . Helper::fieldValue($activity->entity, 'name') . '</li>';
      }
      $activities .= '</ul>';

      $sections[] = [
        'title' => t('All Activities'),
        'into' => "",
        'id' => t('All Activities'),
        'path' => '@organisms/by-author/rich-text.twig',
        'data' => [
          'richText' => [
            'property' => 'description',
            'rteElements' => [
              [
                'path' => '@atoms/11-text/paragraph.twig',
                'data' => [
                  'paragraph' => [
                    'text' => $activities,
                  ],
                ],
              ],
            ],
          ],
        ],
      ];
    }

    // Facilities section.
    if (Helper::isFieldPopulated($entity, $fields['facilities'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_location_facilities']);
    }

    // Services section.
    if (Helper::isFieldPopulated($entity, $fields['services']) && $locationType == 'general') {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_services']);
    }

    // Accessibility section.
    if (Helper::isFieldPopulated($entity, $fields['accessibility'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_accessibility']);
    }

    // Restrictions section.
    if (Helper::isFieldPopulated($entity, $fields['restrictions'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_restrictions']);
    }

    // More info section.
    if (Helper::isFieldPopulated($entity, $fields['information'])) {
      $sections[] = Organisms::prepareRichText($entity, ['field' => 'field_location_more_information']);
    }

    return [
      'sections' => $sections,
    ];
  }

  /**
   * Returns the variables structure required to render a location banner.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param object $options
   *   The object that contains static data and other options.
   *
   * @see @organisms/by-template/location-banner.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    [
   *      "bgTitle":"Mt Greylock State Park"
   *      "bgWide":"/assets/images/placeholder/1600x400.png"
   *      "bgNarrow":"/assets/images/placeholder/800x400.png",
   *      "actionMap": "map",
   *    ]
   */
  public static function prepareLocationBanner($entity, $options = NULL) {
    $locationBanner = [];

    // Create the map of all possible field names to use.
    $map = [
      'markers' => ['field_maps'],
      'bg_wide' => ['field_bg_wide'],
      'bg_narrow' => ['field_bg_narrow'],
      'contact_info' => ['field_ref_contact_info_1'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $locationBanner['bgTitle'] = "";
    // Use helper function to get the image url of a given image style.
    $locationBanner['bgWide'] = Helper::getFieldImageUrl($entity, 'action_banner_large', $fields['bg_wide']);
    $locationBanner['bgNarrow'] = Helper::getFieldImageUrl($entity, 'action_banner_small', $fields['bg_narrow']);
    $locationEntities = Helper::getReferencedEntitiesFromField($entity, $fields['contact_info']);
    $locationBanner['actionMap'] = Molecules::prepareGoogleMapFromContacts($locationEntities);

    return $locationBanner;
  }

  /**
   * Returns the variables structure required to render a location banner.
   *
   * @param array $entities
   *   An array of objects that contains the necessary fields.
   * @param array $options
   *   The array that contains static data and other options.
   *
   * @see @organisms/by-author/mapped-locations.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    "mappedLocations": [
   *      "compHeading": [
   *        "title": "All Locations",
   *        "sub": "",
   *        "color": "",
   *        "id": "",
   *        "centered": ""
   *      ],
   *    "link": [
   *      "href": "#",
   *      "text":"See a list of all locations",
   *      "info": "",
   *      "property": ""
   *    ],
   *    "googleMap": [
   *      "map": [
   *        "center": [
   *          "lat": 42.366565,
   *          "lng": -71.058940
   *        ],
   *        "zoom": 16
   *      ],
   *      "markers": [
   *      [
   *          "position": [
   *            "lat": 42.366565,
   *            "lng": -71.058940
   *          ],
   *        "label": "A",
   *        "infoWindow": [
   *          "name": "Department of Conservation and Recreation",
   *          "phone": "16176261250",
   *          "fax": "16176261351",
   *          "email": "mass.parks@state.ma.us",
   *          "address": "251 Causeway Street, Suite 900\nBoston, MA 02114-2104"
   *        ]
   *      ],
   *    ]]
   *   ]
   *   ]
   */
  public static function prepareMappedLocations(array $entities, array $options) {
    $mapData = [];
    $contact_entities = [];

    foreach ($entities as $entity) {
      $map = [
        'contact_info' => ['field_ref_contact_info_1'],
      ];
      $fields = Helper::getMappedFields($entity, $map);

      $contact_entities = array_merge(Helper::getReferencedEntitiesFromField($entity, $fields['contact_info']), $contact_entities);
    }

    $link = [
      'href' => $options['locationDetailsLink']['path'] . '/locations',
      'text' => t('Location Details'),
      'chevron' => 'true',
    ];

    $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];

    $link = isset($options['locationDetailsLink']['display']) ? $link : [];

    $googleMap = Molecules::prepareGoogleMapFromContacts($contact_entities);

    return array_merge($heading, ['googleMap' => $googleMap, 'link' => $link]);
  }

  /**
   * Returns the variables structure required for richText.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   This is an array of field names.
   *
   * @see @organsms/by-author/rich-text.twig
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareRichText($entity, array $options = []) {
    $richTextWithTitle = [];

    foreach ($options as $field) {
      if (Helper::isFieldPopulated($entity, $field)) {
        $richTextWithTitle = [
          'title' => $entity->$field->getFieldDefinition()->getLabel(),
          'into' => "",
          'id' => $entity->$field->getFieldDefinition()->getLabel(),
          'path' => '@organisms/by-author/rich-text.twig',
          'data' => [
            'richText' => [
              'property' => 'description',
              'rteElements' => [
                [
                  'path' => '@atoms/11-text/paragraph.twig',
                  'data' => [
                    'paragraph' => [
                      'text' => $entity->$field->value,
                    ],
                  ],
                ],
              ],
            ],
          ],
        ];
      }
    }
    return $richTextWithTitle;
  }

  /**
   * Returns the variables structure required to render rich text paragraphs.
   *
   * @param object $entity
   *   The object that contains the fields.
   *
   * @see @organisms/by-author/rich-text.twig
   * @see @atoms/11-text/paragraph.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    "teaserText": {
   *      "richText": {
   *        "rteElements": [{
   *          "path": "@atoms/11-text/paragraph.twig",
   *          "data": {
   *            "paragraph" : {
   *              "text": "Need some help staying on your feet..."
   *            }
   *          }
   *        }]
   *      },
   *      "decorativeLink": {
   *        "href": "#",
   *        "text": "Learn More",
   *        "info": " about unemployment Benefits",
   *        "property": ""
   *      }
   *    }
   */
  public static function prepareTeaserText($entity) {
    $teaserText = [];

    $teaserText['richText'] = Atoms::preparePageContentParagraph($entity);

    return $teaserText;
  }

  /**
   * Returns the variables structure required for richText paragraph.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param string $field
   *   The field name.
   * @param array $options
   *   This is an array of field names.
   *
   * @see @organsms/by-author/rich-text.twig
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareRichTextParagraph($entity, $field = '', array $options = []) {
    $map = [
      'field' => [$field],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);
    return [
      'path' => '@organisms/by-author/rich-text.twig',
      'data' => [
        'richText' => [
          'rteElements' => [
            [
              'path' => '@atoms/11-text/paragraph.twig',
              'data' => [
                'paragraph' => [
                  'text' => $entity->$fields['field']->value,
                ],
              ],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Returns the variables structure required to render form downloads.
   *
   * @param object $entity
   *   The object that contains the field.
   * @param array $options
   *   An array of options.
   *
   * @see @organisms/by-author/form-download.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareFormDownloads($entity, array $options = []) {
    $downloadLinks = [];

    $map = [
      'downloads' => [
        'field_downloads',
        'field_guide_section_downloads',
        'field_service_file',
        'field_action_downloads',
        'field_next_step_downloads',
        'field_how_to_files',
        'field_section_downloads',
        'field_event_ref_downloads',
        'field_executive_order_downloads',
        'field_decision_download',
        'field_advisory_download',
        'field_regulation_download',
      ],
      'link' => [
        'field_guide_section_link',
        'field_service_links',
        'field_section_links',
        'field_event_links',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Roll up our items.
    if (array_key_exists('link', $fields)) {
      foreach ($entity->$fields['link'] as $item) {
        $downloadLinks[] = Molecules::prepareDownloadLink($item, $options);
      }
    }

    foreach ($entity->$fields['downloads'] as $item) {
      $downloadLinks[] = Molecules::prepareDownloadLink($item->entity, $options);
    }

    $heading = isset($options['heading']) ? Helper::buildHeading($options['heading']) : [];

    return array_merge($heading, ['downloadLinks' => $downloadLinks]);
  }

  /**
   * Returns the variables structure required for split columns.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   This is an array of field names.
   *
   * @see @organisms/by-author/split-columns.twig
   *
   * @return array
   *   Returns structured array.
   */
  public static function prepareSplitColumns($entity, array $options = []) {
    $map = [
      'heading' => [
        'field_guide_section_heading_1',
        'field_guide_section_heading_2',
        'field_guide_section_heading_3',
      ],
      'body' => [
        'field_guide_section_body_first',
        'field_guide_section_body_second',
        'field_guide_section_body_third',
      ],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    $splitColumns = [];

    foreach ($map['heading'] as $index => $field_name) {
      if (Helper::isFieldPopulated($entity, $field_name)) {
        $splitColumns['columns'][]['items'] = [
          $header[] = Atoms::prepareColumnHeading($entity->$field_name->value),
          $body[] = Atoms::prepareRawHtml($entity, ['field' => $map['body'][$index]]),
        ];
      }
    }

    return [
      'path' => '@organisms/by-author/split-columns.twig',
      'data' => [
        'splitColumns' => $splitColumns,
      ],
    ];
  }

  /**
   * Returns the variables structure required to render tabularData.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   This is an array of field names and other static options.
   *
   * @see @organisms/by-author/tabular-data.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareTabularData($entity, array $options) {
    $tabularData = [];

    $items = [];

    // Creates a map of fields on the parent entity.
    $map = [
      'fees' => ['field_how_to_ref_fees'],
    ];

    // Creates a map of fields that are on the entitiy.
    $map_ref = [
      'name' => ['field_fee_name'],
      'fee' => ['field_fee_fee'],
      'unit' => ['field_fee_unit'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    foreach ($map_ref as $indexFieldName => $fieldName) {
      $head['rows'][$index]['cells'][] = [
        'heading' => TRUE,
        'text' => ucfirst($indexFieldName),
      ];
    }

    foreach ($entity->get($fields['fees'])->referencedEntities() as $index => $feeEntity) {

      // Determines which fieldnames to use from the map.
      $field = Helper::getMappedFields($feeEntity, $map_ref);
      // Set our dollar sign.
      setlocale(LC_MONETARY, 'en_US.UTF-8');

      foreach ($map_ref as $indexFieldName => $fieldName) {
        $items[$index]['rows'][$index]['cells'][] = [
          'heading' => FALSE,
          'text' => ($indexFieldName == 'fee') ? money_format('%.2n', Helper::fieldValue($feeEntity, $field[$indexFieldName])) : Helper::fieldValue($feeEntity, $field[$indexFieldName]),
        ];
      }
    }

    // Only render heading and table column headings when there is table data.
    if (!empty($items)) {
      $heading = Helper::buildHeading($options['heading']);
      $tabularData = array_merge($heading, ['table' => ['head' => $head, 'bodies' => $items]]);
    }

    return $tabularData;
  }

  /**
   * Returns the variables structure required to render actionActivities.
   *
   * @param object $entities
   *   An EntityReferenceRevisionsFieldItemList that contains the entities.
   *
   * @see @organisms/by-author/image-promos.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareImagePromos($entities) {
    $items = [];

    // Activities section.
    foreach ($entities as $entity) {
      $activityEntity = $entity->entity;

      // Creates a map of fields that are on the entitiy.
      $map = [
        'image' => ['field_image'],
        'title' => ['field_title'],
        'lede' => ['field_lede', 'field_teaser'],
        'link' => ['field_ref_location_details_page'],
      ];

      // Determines which fieldnames to use from the map.
      $fields = Helper::getMappedFields($activityEntity, $map);

      $items[] = Molecules::prepareImagePromo($activityEntity, $fields, []);
    }

    return ['items' => $items];
  }

  /**
   * Returns the variables structure required to render steps ordered.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   This is an array of field names.
   *
   * @see @organisms/by-author/steps-ordered.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareStepsOrdered($entity, array $options = []) {
    $steps = [];

    // Creates a map of fields on the parent entity.
    $map = [
      'reference' => ['field_action_step_numbered_items', 'field_how_to_next_steps'],
    ];

    // Determines which fieldnames to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Retrieves the referenced field from the entity.
    $items = Helper::getReferencedEntitiesFromField($entity, $fields['reference']);

    // Creates a map of fields that are on the referenced entitiy.
    $referenced_fields_map = [
      'title' => ['field_title', 'field_next_step_title'],
      'content' => ['field_content', 'field_next_step_details'],
      'downloads' => ['field_next_step_downloads'],
      'more_link' => ['field_next_step_link'],
    ];

    // Determines the fieldsnames to use on the refrenced entity.
    $referenced_fields = Helper::getMappedReferenceFields($items, $referenced_fields_map);

    // Roll up our action steps.
    if (!empty($items)) {
      foreach ($items as $id => $item) {
        // Set up action step options.
        $step_options = [
          'accordion' => FALSE,
          'expanded' => TRUE,
        ];

        $steps[] = Molecules::prepareActionStep($item, $referenced_fields, $step_options);
      }
    }

    // Build either sidebar or comp heading based on heading type option.
    $heading = [];
    if (isset($options['heading']['type'])) {
      $heading = Helper::buildHeading($options['heading']);
    }

    return [
      'steps' => $steps,
    ] + $heading;
  }

  /**
   * Returns the variables structure required to render steps unordered.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   This is an array of field names.
   *
   * @see @organisms/by-author/steps-unordered.twig
   *
   * @return array
   *   Returns a structured array.
   */
  public static function prepareStepsUnordered($entity, array $options = []) {
    $steps = [];

    // Creates a map of fields on the parent entity.
    $map = [
      'reference' => ['field_how_to_methods_5'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    // Retrieves the referenced field from the entity.
    $items = Helper::getReferencedEntitiesFromField($entity, $fields['reference']);

    // Creates a map of fields that are on the referenced entitiy.
    $referenced_fields_map = [
      'title' => ['field_method_type'],
      'content' => ['field_method_details'],
    ];

    // Determines the field names to use on the referenced entity.
    $referenced_fields = Helper::getMappedReferenceFields($items, $referenced_fields_map);

    // Map method types to icon names.
    $icon_map = [
      'online' => 'laptop',
      'phone' => 'phone',
      'mail' => 'mail',
      'fax' => 'fax-icon',
      'in person' => 'profile',
    ];

    // Roll up our action steps.
    if (!empty($items)) {
      foreach ($items as $id => $item) {
        // Get the icon path for the given method.
        $title = $item->get($referenced_fields['title'])->value;
        $icon_path = Helper::getIconPath($icon_map[$title]);

        // Set up action step options.
        $step_options = [
          'icon_path' => $icon_path,
          'accordion' => TRUE,
          'expanded' => FALSE,
          'label' => "Expand {$title} step.",

        ];

        $steps[] = Molecules::prepareActionStep($item, $referenced_fields, $step_options);
      }
    }

    // Build either sidebar or comp heading based on heading type option.
    $heading = [];
    if (isset($options['heading']['type'])) {
      $heading = Helper::buildHeading($options['heading']);
    }

    return [
      'steps' => $steps,
    ] + $heading;
  }

}
