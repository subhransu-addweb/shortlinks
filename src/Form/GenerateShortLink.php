<?php

namespace Drupal\shortlinks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Use Drupal\Core\Ajax\AjaxResponse;
// use Drupal\Core\Ajax\HtmlCommand;
// use Drupal\Core\Ajax\InvokeCommand;.

/**
 * Class GenerateShortLink generate shortlink form.
 */
class GenerateShortLink extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shortlink_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>',
    ];

    $form['web_address'] = [
      '#type' => 'textfield',
      '#title' => t('Web address without http:// and https://'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Shorten',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $web_address = trim($form_state->getValue('web_address'));

    if (!GenerateShortLink::isValidDomainName($web_address)) {

      $form_state->setErrorByName('web_address', $this->t('Please enter a valid web address'));

    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    global $base_url;

    $fields = $form_state->getValues();

    $web_address = $fields['web_address'];

    $ip_address = \Drupal::request()->getClientIp();

    $uid = \Drupal::currentUser()->id();

    $link_identifier = GenerateShortLink::randomAlphanumeric(9);

    $generated_links = $base_url . '/target/' . $link_identifier;

    $field = [
      'web_address'   => $web_address,
      'link_identifier' => $link_identifier,
      'generated_links' => $generated_links,
      'uid' => $uid,
      'ip' => $ip_address,
      'created' => strtotime("now"),
      'redirect_count' => 0,
    ];

    $db = Database::getConnection();

    $db->insert('shortlinks')
      ->fields($field)
      ->execute();

    \Drupal::messenger()->addMessage(t('Record Inserted Successfully'));

    $response = new RedirectResponse('view/' . $link_identifier);
    $response->send();
    exit(0);

  }

  /**
   * {@inheritdoc}
   */
  public function randomAlphanumeric($length) {
    $chars = '0124abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345689';
    $my_string = '';
    for ($i = 0; $i < $length; $i++) {
      $pos = mt_rand(0, strlen($chars) - 1);
      $my_string .= substr($chars, $pos, 1);
    }
    return $my_string;
  }

  /**
   * Domain name validate.
   */
  public function isValidDomainName($domain_name) {
    // Valid chars check.
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name)
    // Overall length check.
                && preg_match("/^.{1,253}$/", $domain_name)
    // Length of each label.
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name));
  }

}
