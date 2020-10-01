<?php

namespace Drupal\shortlinks\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Database\Database;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "myshortlinks_resource",
 *   label = @Translation("Create Shortlinks resource"),
 *   uri_paths = {
 *     "canonical" = "api/create-shortlinks-resource",
 *     "https://www.drupal.org/link-relations/create" = "/api/create-shortlinks-resource"
 *   }
 * )
 */
class MyshortlinksResources extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new CreateNodeResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('shortlinks'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $response = [];
    $web = $data['web_addr'];

    // Check valid domain name.
    if (MyshortlinksResources::isValidDomainName($web)) {
      // Genertae random string.
      global $base_url;
      /*get form filed values*/
      $ip_address = \Drupal::request()->getClientIp();

      $uid = 0;

      $link_identifier = MyshortlinksResources::randomAlphanumeric(9);

      $generated_links = $base_url . '/target/' . $link_identifier;

      /*Insert new record*/
      $field = [
        'web_address'   => $web,
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

      $response['status'] = 1;
      $response['shortlinks'] = $generated_links;
    }
    else {
      $response['status'] = 0;
      $response['error'] = 'Invalid domain name provided or provide domain name without http://';
    }

    return new ModifiedResourceResponse($response);
  }

  /**
   * Generate random 9 digit string.
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
