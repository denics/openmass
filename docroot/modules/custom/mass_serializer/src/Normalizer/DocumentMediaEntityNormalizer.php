<?php

namespace Drupal\mass_serializer\Normalizer;

use Drupal\serialization\Normalizer\ContentEntityNormalizer;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\media_entity\MediaInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Converts the Drupal entity object structures to a normalized array.
 */
class DocumentMediaEntityNormalizer extends ContentEntityNormalizer {
  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\media_entity\MediaInterface';

  /**
   * The date format for output.
   *
   * @var string
   */
  protected $dateFormat = 'Y-m-d';

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    // If we aren't dealing with an object or the format is not supported return
    // now.
    if (!is_object($data) || !$this->checkFormat($format)) {
      return FALSE;
    }
    // This custom normalizer should be supported for "document" media entities.
    if ($data instanceof MediaInterface) {
      // TODO: && $data->getType() == 'document') {.
      return TRUE;
    }
    // Otherwise, this normalizer does not support the $data object.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($entity, $format = NULL, array $context = []) {
    $attributes = parent::normalize($entity, $format, $context);

    $attributes['@type'] = 'dcat:Dataset';
    $attributes['accessLevel'] = 'public';

    if (!empty($attributes['field_link_classic_massgov'][0]['uri'])) {
      $attributes['classicMassGovURL'] = $attributes['field_link_classic_massgov'][0]['uri'];
    }

    // Set contactPoint.
    $contact_tid = $attributes['field_contact_name'][0]['target_id'];
    if (!is_null($contact_tid)) {
      $contact = Term::load($contact_tid);
      $attributes['contactPoint']['@type'] = 'vcard:Contact';
      $attributes['contactPoint']['fn'] = $contact->getName();
      if(!is_null($attributes['field_contact_information'][0])) {
        $attributes['contactPoint']['hasEmail'] = "mailto:" . $attributes['field_contact_information'];
      }
    }
    else {
      $attributes['contactPoint'] = new \stdClass();
    }

    $attributes['dataQuality'] = FALSE;
    $attributes['description'] = !empty($attributes['field_description']) ? $attributes['field_description'] : '';

    // Set distribution.
    for ($i = 0; $i < count($attributes['field_upload_file']); $i++) {
      $fid = $attributes['field_upload_file'][$i]['target_id'];
      if (!is_null($fid)) {
        $file = File::load($fid);
        $attributes['distribution'][$i]['@type'] = 'dcat:Distribution';
        $attributes['distribution'][$i]['title'] = $attributes['name'];
        $attributes['distribution'][$i]['downloadURL'] = $file->url();
        $attributes['distribution'][$i]['mediaType'] = $attributes['field_file_mime'];
      }
    }

    $attributes['identifier'] = $entity->toUrl('canonical', ['absolute' => TRUE])->toString();

    $attributes['issued'] = date($this->dateFormat, $attributes['created']);

    // Set keyword.
    $attributes['keyword'] = [];
    for ($i = 0; $i < count($attributes['field_tags']); $i++) {
      $tags_tid = $attributes['field_tags'][$i]['target_id'];
      if (!is_null($tags_tid)) {
        $tag = Term::load($tags_tid);
        $attributes['keyword'][] = $tag->getName();
      }
    }

    $attributes['modified'] = date($this->dateFormat, $attributes['changed']);

    // Set publisher.
    $creator_tid = $attributes['field_creator'][0]['target_id'];
    if (!is_null($creator_tid)) {
      $creator = Term::load($creator_tid);
      $attributes['publisher']['name'] = $creator->getName();
    }
    else {
      $attributes['publisher'] = new \stdClass();
    }

    if (!empty($attributes['field_link_related_content'][0]['uri'])) {
      $attributes['references'][] = $attributes['field_link_related_content'][0]['uri'];
    }

    $attributes['spatial'] = $attributes['field_geographic_place'];

    // The temporal coverage must have a start and end date.
    if (!is_null($attributes['field_end_date'][0])) {
      $attributes['temporal'] = $attributes['field_start_date'] . '/' . $attributes['field_end_date'];
    }
    else {
      $attributes['temporal'] = $attributes['field_start_date'] . '/' . $attributes['modified'];
    }

    // UUID follows title.
    $attributes['title'] = $attributes['field_title'];

    // Unset unused attributes.
    unset($attributes['bundle']);
    unset($attributes['created']);
    unset($attributes['changed']);
    unset($attributes['default_langcode']);
    unset($attributes['field_contact_name']);
    unset($attributes['field_contributing_agency']);
    unset($attributes['field_creator']);
    unset($attributes['field_description']);
    unset($attributes['field_contact_information']);
    unset($attributes['field_end_date']);
    unset($attributes['field_file_mime']);
    unset($attributes['field_geographic_place']);
    unset($attributes['field_link_classic_massgov']);
    unset($attributes['field_internal_notes']);
    unset($attributes['field_link_related_content']);
    unset($attributes['field_publishing_frequency']);
    unset($attributes['field_size']);
    unset($attributes['field_start_date']);
    unset($attributes['field_tags']);
    unset($attributes['field_title']);
    unset($attributes['field_upload_file']);
    unset($attributes['langcode']);
    unset($attributes['moderation_state']);
    unset($attributes['mid']);
    unset($attributes['name']);
    unset($attributes['revision_log']);
    unset($attributes['revision_timestamp']);
    unset($attributes['revision_uid']);
    unset($attributes['status']);
    unset($attributes['thumbnail']);
    unset($attributes['uid']);
    unset($attributes['vid']);

    // Re-sort the array after our new addition.
    ksort($attributes);
    // Return the $attributes with our new value.
    return $attributes;
  }

}
