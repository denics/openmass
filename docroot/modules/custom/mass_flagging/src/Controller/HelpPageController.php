<?php

namespace Drupal\mass_flagging\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelpPageController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {
    module_load_include('inc', 'mass_flagging');
    return array(
      '#type' => 'markup',
      '#markup' => get_mass_flagging_help_text(),
    );
  }
}