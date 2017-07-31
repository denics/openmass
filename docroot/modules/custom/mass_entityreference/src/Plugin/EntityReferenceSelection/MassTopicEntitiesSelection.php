<?php

namespace Drupal\mass_entityreference\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "mass_topics",
 *   label = @Translation("Topic: Common Content"),
 *   group = "mass_topics",
 *   weight = 0
 * )
 */
class MassTopicEntitiesSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    if ($nid = $_COOKIE['Drupal_visitor_topic_nid']) {

      // Get all subtopics associated with the action.
      $subtopic_query = \Drupal::entityQuery('node')
        ->condition('field_topic_parent.target_id', $nid);

      $nids = $subtopic_query->execute();

      // Use subtopics to find associated actions.
      $query = parent::buildEntityQuery($match, $match_operator);
      $query->condition('field_action_parent.target_id', $nids, "IN");
      return $query;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {}

}
