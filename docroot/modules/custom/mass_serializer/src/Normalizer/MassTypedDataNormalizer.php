<?php

namespace Drupal\mass_serializer\Normalizer;

use Drupal\serialization\Normalizer\NormalizerBase;

/**
 * Converts typed data objects to arrays.
 */
class MassTypedDataNormalizer extends NormalizerBase {
  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\Core\TypedData\TypedDataInterface';

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $value = $object->getValue();
    if (isset($value[0]) && isset($value[0]['value'])) {
      $value = $value[0]['value'];
    }
    return $value;
  }

}
