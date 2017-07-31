<?php

namespace Drupal\mayflower\Prepare;

use Drupal\mayflower\Helper;

/**
 * Provides variable structure for schema.org objects using prepare functions.
 */
class Schemas {

  /**
   * Returns variable structure to render governmentOrganization schema data.
   *
   * @param array $variables
   *   An entity that has been preprocessed.
   *
   * @see @meta/schema/government-organization.twig
   *
   * @return array
   *   Returns an array of items that contains:
   *   "governmentOrganization": {
   *      "name": "Executive Office of Health and Human Services",
   *      "alternateName": "EOHHS",
   *      "memberOf": {
   *        "id": "http://pilot.mass.gov/#organization"
   *      },
   *      "disambiguatingDescription": "EOHHS oversees health and general...",
   *      "description": "EOHHS serves...",
   *      "logo": "https://pilot.mass.gov/assets/images/230x130.png"
   *      "url": "https://pilot.mass.gov/?p=templates-org-landing-page",
   *      "contactInfo": [
   *        "address": "One Ashburton Place, 11th Floor, Boston, MA 02108",
   *        "telephone": "+14134994262",
   *        "faxNumber": "+14134994266",
   *        "email": "email@email.com"
   *      ],
   *      "sameAs": [
   *          "https://twitter.com/MassHHS",
   *          "https://www.flickr.com/photos/mass_hhs/",
   *          "https://blog.mass.gov/hhs"
   *      ]
   *   }
   */
  public static function prepareGovernmentOrganization(array $variables) {
    $metatags = Helper::addMetatagData(['description' => '']);

    // @TODO find a shared location for this, we'll need them in every schema.
    $current_path = Helper::getCurrentPathAlias();
    $hostname = \Drupal::request()->getSchemeAndHttpHost();

    $schema = array_key_exists('schema', $variables) ? $variables['schema'] : [];
    $schema['governmentOrganization'] = [];

    // Set an id for this schema.
    $schema['governmentOrganization']['id'] = $hostname . $current_path . "/#governmentOrganization";

    // Use page title as organization name.
    $schema['governmentOrganization']['name'] = array_key_exists('title', $variables['pageBanner']) ? $variables['pageBanner']['title'] : '';

    // Org page memberOf.id maps to global org set in html preprocess in .theme.
    $schema['governmentOrganization']['memberOf']['id'] = $hostname . "/#organization";

    // Use the optional acronym as alternate name.
    $schema['governmentOrganization']['alternateName'] = array_key_exists('titleSubText', $variables['pageBanner']) ? $variables['pageBanner']['titleSubText'] : '';

    // Use first line of optional "who we serve" as disambiguating description.
    if (isset($variables['stackedRowSections'][0]) && array_key_exists('title', $variables['stackedRowSections'][0]) && $variables['stackedRowSections'][0]['title'] === "Who We Serve") {
      $schema['governmentOrganization']['disambiguatingDescription'] = array_key_exists('rawHtml', $variables['stackedRowSections'][0]['pageContent'][0]['data']['richText']['rteElements'][0]['data']) ? Helper::getFirstParagraph($variables['stackedRowSections'][0]['pageContent'][0]['data']['richText']['rteElements'][0]['data']['rawHtml']['content']['#text']) : '';
    }

    // Use the metatags module description value for the description property.
    $schema['governmentOrganization']['description'] = !empty($metatags['description']) ? $metatags['description'] : Helper::getFirstParagraph($variables['pageHeader']['subTitle']);

    // Use the optional logo url as the logo property.
    if (array_key_exists('widgets', $variables['pageHeader']) && !empty($variables['pageHeader']['widgets'])) {
      $schema['governmentOrganization']['logo'] = array_key_exists('image', $variables['pageHeader']['widgets'][0]['data']) ? Helper::sanitizeUrlCacheString($variables['pageHeader']['widgets'][0]['data']['image']['src'], "?itok=") : '';
    }

    // Use the current host + path alias as URL.
    $schema['governmentOrganization']['url'] = $hostname . $current_path;
    // Map first contactUs values to contact properties (address, phone, etc.).
    // @see Organisms::prepareContactUs + Molecules::prepareContactGroup
    $schema['governmentOrganization']['contactInfo'] = array_key_exists('contactUs', $variables['pageHeader']['optionalContents']) &&  array_key_exists('schemaContactInfo', $variables['pageHeader']['optionalContents']['contactUs']) ?
      Schemas::prepareContactInfo($variables['pageHeader']['optionalContents']['contactUs']['schemaContactInfo']) : '';

    // Get the social media links, if that component was used.
    if (isset($variables['stackedRowSections'][0]) && array_key_exists('sideBar', $variables['stackedRowSections'][0]) && !empty($variables['stackedRowSections'][0]['sideBar'])) {
      $schema['governmentOrganization']['sameAs'] = array_key_exists('iconLinks', $variables['stackedRowSections'][0]['sideBar'][1]['data']) ? Schemas::prepareSameAs($variables['stackedRowSections'][0]['sideBar'][1]['data']['iconLinks']['items']) : '';
    }

    return $schema;
  }

  /**
   * Returns the social media asset urls array for sameAs schema property.
   *
   * @param array $socialLinks
   *   Prepared social media links, returned by Molecules::prepareIconLinks.
   *
   * @return array
   *   Returns an array of items that can be used for sameAs property, contains:
   *   [
   *      "https://twitter.com/MassHHS",
   *      "https://www.flickr.com/photos/mass_hhs/",
   *      "https://blog.mass.gov/hhs"
   *   ]
   */
  protected static function prepareSameAs(array $socialLinks) {
    $sameAs = [];
    foreach ($socialLinks as $link) {
      if (array_key_exists('href', $link['link'])) {
        $sameAs[] = $link['link']['href'];
      }
    }
    return $sameAs;
  }

  /**
   * Returns an object with schema contact info.
   *
   * @param array $schemaContactInfo
   *   Array of schema contact info returned by Molecules::prepareContactUs.
   *
   * @return array
   *   Returns an array of property/values that the govOrg schema is expecting
   *   contactInfo = [
   *      "address": "One Ashburton Place, 11th Floor, Boston, MA 02108",
   *      "telephone": "+14134994262",
   *      "faxNumber": "+14134994266",
   *      "email": "email@email.com",
   *   ]
   */
  protected static function prepareContactInfo(array $schemaContactInfo) {
    $contactInfo = [];

    $contactInfo["address"] = trim(preg_replace('/\s+/', ' ', $schemaContactInfo['address']));
    $contactInfo["hasMap"] = $schemaContactInfo['hasMap'];
    $contactInfo["telephone"] = $schemaContactInfo['phone'];
    $contactInfo["faxNumber"] = $schemaContactInfo['fax'];
    $contactInfo["email"] = $schemaContactInfo['email'];

    return $contactInfo;
  }

}
