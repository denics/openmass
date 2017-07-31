<?php

namespace Drupal\mass_validation\Plugin\Validation\Constraint;

/**
 * @file
 * Contains PreventEmptyImageConstraintValidator class.
 */

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the PreventEmptyImage constraint.
 */
class PreventEmptyImageConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    /** @var \Drupal\mass_validation\Plugin\Validation\Constraint\SocialLinkConstraint $constraint */
    if (!isset($entity)) {
      return;
    }

    if ($entity->hasField('field_action_set__bg_wide') &&
      $entity->hasField('field_ref_actions_3') &&
      $entity->hasField('field_ref_actions_6') &&
      $entity->get('field_action_set__bg_wide')->isEmpty()
      && (!$entity->get('field_ref_actions_3')->isEmpty() || !$entity->get('field_ref_actions_6')->isEmpty())) {
      $this->context->buildViolation($constraint->message)
        ->atPath('field_action_set__bg_wide')
        ->addViolation();
    }
  }

}
