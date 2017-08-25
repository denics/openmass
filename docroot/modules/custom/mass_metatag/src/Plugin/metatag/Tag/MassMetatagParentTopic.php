<?php

namespace Drupal\mass_metatag\Plugin\metatag\Tag;

use Drupal\metatag\Plugin\metatag\Tag\MetaNameBase;
use Drupal\node\Entity\Node;

/**
 * Provides a plugin for the 'mg_parent_topic' meta tag.
 *
 * @MetatagTag(
 *   id = "mass_metatag_parent_topic",
 *   label = @Translation("mg_parent_topic"),
 *   description = @Translation("The ID used to find the parent topic for this page."),
 *   name = "mg_parent_topic",
 *   group = "mass_metatag",
 *   weight = -1,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class MassMetatagParentTopic extends MetaNameBase {

  /**
   * Preprocess output.
   */
  public function output() {
    $element = parent::output();
    if (!empty($element)) {
      $source_nid = $element['#attributes']['content'];
      if (is_numeric($source_nid)) {
        // Get the title of the topic that references this node.
        $query = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('type', 'topic_page')
          ->condition('field_topic_content_cards.entity.field_content_card_link_cards.uri', 'entity:node/' . $source_nid);
        $nids = $query->execute();
        $topic_arr = [];
        foreach ($nids as $nid) {
          $topic = Node::load($nid);
          $topic_arr[] = $topic->title->value;
        }
        $topic_titles = implode(', ', $topic_arr);
        $element['#attributes']['content'] = $topic_titles;
      }
    }
    return $element;
  }

}
