<?php

namespace Drupal\mass_migration\Plugin\migrate\process;

use Drupal\field\Entity\FieldConfig;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Given the access level value returns the corresponding key.
 *
 * @MigrateProcessPlugin(
 *   id = "mass_migration_get_access_level_key",
 *   handle_multiples = TRUE
 * )
 */
class GetAccessLevelKey extends ProcessPluginBase {
  /**
   * Allowed values for Access Level field.
   *
   * @var array
   */
  private $accessLevelValues;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->accessLevelValues = FieldConfig::loadByName('media', 'document', 'field_public_access_level')->getSettings()['allowed_values'];
  }

  /**
   * {@inheritdoc}
   */
  public function transform($access_level, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Check for empty value.
    if (empty($access_level)) {
      return FALSE;
    }

    // If the value is in the database return the corresponding key.
    $key = array_search($access_level, $this->accessLevelValues);
    return ($key !== FALSE) ? $key : FALSE;
  }

}
