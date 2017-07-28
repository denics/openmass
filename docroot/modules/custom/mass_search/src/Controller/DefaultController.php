<?php

namespace Drupal\mass_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 *
 * @package Drupal\mass_search\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Search.
   *
   * @return array
   *   Render array that calls mass-search template and
   *   attaches the js library to draw the search results page
   *   search form + results
   */
  public function search() {
    return [
      '#theme' => 'mass_search',
      '#attached' => [
        'library' => [
          'mass_search/google-cse',
        ],
      ],
    ];
  }

}
