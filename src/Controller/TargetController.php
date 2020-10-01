<?php

namespace Drupal\shortlinks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DisplayTableContrller.
 *
 * @package Drupal\custom_crud\Controller
 */
class TargetController extends ControllerBase {

  /**
   * Return table data.
   */
  public function processlink($shrt = NULL) {

    $db = Database::getConnection();

    if (isset($shrt)) {

      $results = $db->select('shortlinks', 'm')
        ->fields('m', ['web_address', 'link_identifier'])
        ->condition('m.link_identifier', $shrt)
        ->execute()
        ->fetchAssoc();

      if (is_array($results) && count($results) > 0) {

        $webadr = $results['web_address'];

        $webadr = 'http://' . $webadr;

        $update = $db->update('shortlinks')
          ->expression('redirect_count', 'redirect_count + :inc', [':inc' => 1])
          ->condition('link_identifier', $shrt)
          ->execute();

        $ip_address = \Drupal::request()->getClientIp();
        $uid = \Drupal::currentUser()->id();
        $field = [
          'web_address' => $webadr,
          'link_identifier' => $shrt,
          'uid' => $uid,
          'ip' => $ip_address,
          'redirect_time' => strtotime("now"),
        ];
        $db->insert('shortlinks_redirects')
          ->fields($field)
          ->execute();

        $response = new RedirectResponse($webadr);
        $response->send();
        exit(0);

      }
      else {
        echo "Data not found";
        die();
      }
    }

    return TRUE;

  }

}
