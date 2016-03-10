<?php
/**
 * @file
 * Contains \Drupal\storyserver\Plugin\Field\FieldFormatter\StoryServerStoryFormatter
 */

namespace Drupal\storyserver\Plugin\Field\FieldFormatter;

module_load_include('php', 'storyserver', 'vendor/autoload');

use Drupal\storyserver\Client\Client;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *  id = "storyserver_story",
 *  label = @Translation("Story Formatter"),
 *  field_types = {"storyserver_story"}
 * )
 */
class StoryServerStoryFormatter extends FormatterBase {

  /**
   * Http client
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;


  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition,
                              FieldDefinitionInterface $field_definition,
                              array $settings,
                              $label,
                              $view_mode,
                              array $third_party_settings) {

    // Can we implement this via a setter dependency?
    $this->httpClient = \Drupal::service('http_client');

    parent::__construct($plugin_id, $plugin_definition,
                                $field_definition,
                                $settings,
                                $label,
                                $view_mode,
                                $third_party_settings);
  }

//  /**
//   * {@inheritdoc}
//   */
//  public static function create(ContainerInterface $container) {
//    return new static(
//      $container->get('http_client')
//    );
//  }

  /**
   * Helper method to retrieve a StoryServer story for the requested formats.
   * @param $id
   * @param $formats
   * @return array|null
   */
  private function getStory($id, $formats) {
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

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = array();

    foreach ($items as $delta => $item) {
      $values = $item->getValue();
      if(isset($values['story_id']) && isset($values['story_theme'])) {
        $storyId = $values['story_id'];
        $storyTheme = $values['story_theme'];
        $themePath = drupal_get_path('module', 'storyserver') . '/themes/'. $storyTheme;
        $settings = parse_ini_file($themePath . '/story.ini');
        if(!empty($settings) && isset($settings['formats'])) {
          $story = $this->getStory($storyId, $settings['formats']);
          if(!empty($story)) {
            $elements[$delta] = array(
              '#theme' => 'storyserver_theme_' . $values['story_theme'],
              '#story_id' => $storyId,
              '#story_theme' => $storyTheme,
              '#story' => $story['data'],
              '#safeJson' => $story['safeJson'],
              '#appServer' =>  file_create_url($themePath),
              '#attached' => array(
                'library' =>  array(
                  'storyserver/storyserver-theme-' . $storyTheme
                )
              )
            );
          }
        }
      }
    }

    return $elements;
  }

}
