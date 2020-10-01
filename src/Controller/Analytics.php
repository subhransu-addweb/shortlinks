<?php

namespace Drupal\shortlinks\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Class Analytics generate short links report.
 */
class Analytics extends ControllerBase {

  /**
   * Return table data.
   */
  public function display() {

    $header_table = [

      'lid' => t('SrNo'),
      'web_address' => t('web address'),
      'link_identifier' => t('link identifier'),
      'generated_links' => t('generated links'),
      'redirect_count' => t('redirect count'),
      'show_details' => t('Show details'),

    ];

    /*Select records from datababase tables*/
    $db = \Drupal::database();
    $results = $db->select('shortlinks', 'm')
      ->fields('m', ['lid', 'web_address', 'link_identifier',
        'generated_links', 'redirect_count',
      ])
      ->orderby('lid', 'DESC')
      ->execute()
      ->fetchAll();

    $rows = [];

    foreach ($results as $data) {

      $details = Url::FromUserInput('/shortlink-redirect-details/' . $data->link_identifier);

      $rows[] = [
        'lid' => $data->lid,
        'web_address' => $data->web_address,
        'link_identifier' => $data->link_identifier,
        'generated_links' => $data->generated_links,
        'redirect_count' => $data->redirect_count,
        'show_details' => \Drupal::l('Details', $details),
      ];
    }

    /*Dispaly Data in Table*/
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => 'No record found!',
    ];

    return $form;

  }

  /**
   * Display redirect details.
   */
  public function redirectdetails($shrt = NULL) {
    if (isset($shrt)) {

      $header_table = [
        'sono' => t('SrNo'),
        'username' => t('User Name'),
        'redirect_time' => t('Redirection time'),
        'ip' => t('Ip address'),
        'link_identifier' => t('link identifier'),
        'web_address' => t('Full URL'),
      ];

      /*Select records from datababase tables*/
      $db = \Drupal::database();
      $results = $db->select('shortlinks_redirects', 'm')
        ->fields('m', ['web_address', 'link_identifier',
          'uid', 'ip', 'redirect_time',
        ])
        ->condition('m.link_identifier', $shrt)
        ->orderby('lid', 'DESC')
        ->execute()
        ->fetchAll();

      $rows = [];
      $sln = 1;
      foreach ($results as $data) {
        $account = User::load($data->uid);
        $name = $account->getUsername();
        $rows[] = [
          'sono' => $sln,
          'username' => ($data->uid != 0) ? $name : t('Anonymous'),
          'redirect_time' => date('Y-m-d H:i:s', $data->redirect_time),
          'ip' => $data->ip,
          'link_identifier' => $data->link_identifier,
          'web_address' => $data->web_address,
        ];
        $sln = $sln + 1;
      }

      /*Dispaly Data in Table*/
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
        '#empty' => 'No record found!',
      ];

      return $form;
    }
    else {
      return "Invalid shortlinks or no shortlink provided";
    }
  }

}
