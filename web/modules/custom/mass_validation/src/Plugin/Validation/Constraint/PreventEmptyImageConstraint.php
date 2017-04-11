<?php

namespace Drupal\mass_validation\Plugin\Validation\Constraint;

/**
 * @file
 * Contains PreventEmptyImageConstraint class.
 *
 * Copyright 2017 Palantir.net, Inc.
 */

use Drupal\Core\Entity\Plugin\Validation\Constraint\CompositeConstraintBase;

/**
 * Prevent background image to be empty.
 *
 * When Featured or Action Guides have values then the background image
 * can not be empty.
 *
 * @Constraint(
 *   id = "PreventEmptyImage",
 *   label = @Translation("Prevent nodes from being created if background image is empty and Featured or Action Guides have values.", context = "Validation"),
 *   type = "entity:node"
 * )
 */
class PreventEmptyImageConstraint extends CompositeConstraintBase {
  /**
   * Message shown when a node is being created with empty background image.
   *
   * @var string
   */
  public $message = 'Since "Featured Actions" or "All Actions & Guides" fields have content, the "Background Image" field must not be empty.';

  /**
   * {@inheritdoc}
   */
  public function coversFields() {
    return [
      'field_ref_actions_3',
      'field_ref_actions_6',
      'field_action_set__bg_wide',
    ];
  }

}
