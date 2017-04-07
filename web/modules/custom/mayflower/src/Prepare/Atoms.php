<?php

namespace Drupal\mayflower\Prepare;

use Drupal\mayflower\Helper;

/**
 * Provides variable structure for mayflower atoms using prepare functions.
 *
 * Copyright 2017 Palantir.net, Inc.
 */
class Atoms {

  /**
   * Returns the variables structure required to render a the image atom.
   *
   * @param object $entity
   *   The object that contains the necessary fields.
   * @param object $options
   *   The object that contains static data and other options.
   *
   * @see @atoms/09-media/image.twig
   *
   * @return array
   *   Returns an array of items that contain:
   *    "image": {
   *      "alt": "eohhs logo",
   *      "src": "/assets/images/placeholder/230x130.png",
   *      "height": "130",
   *      "width": "230"
   *    }
   */
  public static function prepareImage($entity, $options) {
    $image = '';

    $map = [
      'image' => ['field_sub_brand'],
    ];

    // Determines which field names to use from the map.
    $fields = Helper::getMappedFields($entity, $map);

    if ($src = Helper::getFieldImageUrl($entity, $options['style'], $fields['image'])) {
      // Get image alt text from entity (set to '' on falsey return).
      $image_alt = $entity->$fields['image']->alt ?: '';

      $image = [
        'alt' => $image_alt,
        'src' => $src,
        'height' => $options['height'],
        'width' => $options['width'],
      ];
    }

    return $image;
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
   *    rteElements: [
   *      [
   *        path: @atoms/11-text/paragraph.twig,
   *        data: [
   *          paragraph: [
   *            text: 'My paragraph text.'
   *          ]
   *        ]
   *      ], ...
   *    ]
   */
  public static function preparePageContentParagraph($entity) {
    $paragraphs = [];

    foreach ($entity as $paragraph) {
      $paragraphs[] = [
        'path' => '@atoms/11-text/raw-html.twig',
        'data' => [
          'rawHtml' => [
            'content' => $paragraph->view(),
          ],
        ],
      ];
    }
    return [
      'rteElements' => $paragraphs,
    ];
  }

  /**
   * Returns the variables structure required to render rich text paragraphs.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   An array of options.
   *
   * @see @organisms/by-author/rich-text.twig
   * @see @atoms/11-text/paragraph.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    rteElements: [
   *      [
   *        path: @atoms/11-text/paragraph.twig,
   *        data: [
   *          paragraph: [
   *            text: 'My paragraph text.'
   *          ]
   *        ]
   *      ], ...
   *    ]
   */
  public static function prepareParagraph($entity, array $options) {
    $rteElements = [];

    $field = $options['field'];

    $rteElements = [
      'path' => '@atoms/11-text/paragraph.twig',
      'data' => [
        'paragraph' => [
          'text' => Helper::fieldValue($entity, $field),
        ],
      ],
    ];

    return $rteElements;
  }

  /**
   * Returns the variables structure required to render rich text paragraphs.
   *
   * @param object $entity
   *   The object that contains the fields.
   * @param array $options
   *   An array of options.
   *
   * @see @organisms/by-author/rich-text.twig
   * @see @atoms/11-text/raw-html.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *    rteElements: [
   *      [
   *        path: @atoms/11-text/paragraph.twig,
   *        data: [
   *          raw-html: [
   *            content: 'My paragraph text.'
   *          ]
   *        ]
   *      ], ...
   *    ]
   */
  public static function prepareRawHtml($entity, array $options) {
    $rteElements = [];

    $field = $options['field'];

    $text = Helper::fieldValue($entity, $field);

    // If we have an inline entity, like maybe an image, process.
    if (strpos($text, '<drupal-entity') !== FALSE) {
      $text = Helper::fieldFullView($entity, $field);
    }

    $rteElements = [
      'path' => '@atoms/11-text/raw-html.twig',
      'data' => [
        'rawHtml' => [
          'content' => $text,
        ],
      ],
    ];

    return $rteElements;
  }

  /**
   * Returns the variables structure required to render sidebar heading.
   *
   * @param string $text
   *   A string containing text.
   *
   * @see @atoms/04-headings/sidebar-heading.twig
   *
   * @return array
   *   Returns correct array for sidebarHeading:
   *    [
   *      "sidebarHeading": [
   *        "title": "Social"
   *      ]
   *    ]
   */
  public static function prepareSidebarHeading($text) {
    return [
      'sidebarHeading' => [
        'title' => $text,
      ],
    ];
  }

  /**
   * Returns the variables structure required to render column heading.
   *
   * @param string $text
   *   A string containing text.
   *
   * @see @atoms/04-headings/column-heading.twig
   *
   * @return array
   *   Returns correct array for sidebarHeading:
   *    [
   *      "columnHeading": [
   *        "title": "Social"
   *      ]
   *    ]
   */
  public static function prepareColumnHeading($text) {
    return [
      'path' => '@atoms/04-headings/column-heading.twig',
      'data' => [
        'columnHeading' => [
          'text' => $text,
        ],
      ],
    ];
  }

}
