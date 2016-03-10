<?php

/**
 * @file
 * Contains \StoryServerController.
 */

namespace Drupal\storyserver\Controller;

use Drupal\storyserver\Client\Client;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class StoryServerController
 * @package Drupal\storyserver\Controller
 */
class StoryServerController extends ControllerBase {


  /**
   * Http client
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;


  /**
   * {@inheritdoc}
   */
  public function __construct(\GuzzleHttp\Client $client) {
    $this->httpClient = $client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }


  /**
   * StoryServer story names lookup from the StoryServer API server.
   */
  public function storyNames() {

    $config = \Drupal::config('storyserver.settings');
    $keyId     = $config->get('storyserver_key_id');
    $secretKey = $config->get('storyserver_secret_key');
    $apiServer = $config->get('storyserver_api_server');

    $client = new Client([
      'storyServer' => $apiServer,
      'keyId' => $keyId,
      'secretKey' => $secretKey,
      'formats' => '',
      'appServer'=> ''
    ], $this->httpClient);

    if (!empty($_GET['storyname'])) {
      $q = $_GET['storyname'];
    } else {
      $q = '';
    }

    $result = null;
    try {
      $result = $client->getStoryNames($q);
    } catch (\Exception $e) {
      \Drupal::logger('storyserver')->notice($e->getMessage(), []);
    }

    return new JsonResponse($result);
  }


  /**
   * StoryServer getStory from StoryServer API server.
   * @param $id
   * @param $formats
   * @return array|null
   */
  public function getStory($id, $formats) {

    $config = \Drupal::config('storyserver.settings');
    $keyId     = $config->get('storyserver_key_id');
    $secretKey = $config->get('storyserver_secret_key');
    $apiServer = $config->get('storyserver_api_server');

    $client = new Client([
      'storyServer' => $apiServer,
      'keyId' => $keyId,
      'secretKey' => $secretKey,
      'formats' => $formats,
      'appServer'=> ''
    ], $this->httpClient);

    $result = null;
    try {
      $result = $client->getStoryById($id);
    } catch (\Exception $e) {
      \Drupal::logger('storyserver')->notice($e->getMessage(), []);
    }

    return $result;
  }

}
