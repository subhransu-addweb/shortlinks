<?php

namespace Drupal\shortlinks\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DisplayTableContrller.
 *
 * @package Drupal\custom_crud\Controller
 */
class ShortlinkInfoController extends ControllerBase {

  /**
   * Return table data.
   */
  public function display($shrt = NULL) {

    global $base_url;

    return [
      '#type' => 'markup',
      '#markup' => 'Short link generated from your URL :- ' . $base_url . '/target/' . $shrt,
    ];

  }

}
