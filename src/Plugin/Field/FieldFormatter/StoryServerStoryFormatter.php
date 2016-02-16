<?php
/**
 * @file
 * Contains \Drupal\storyserver\Plugin\Field\FieldFormatter\StoryServerStoryFormatter
 */

namespace Drupal\storyserver\Plugin\Field\FieldFormatter;

module_load_include('php', 'storyserver', 'vendor/autoload');

use StoryServer\Client;
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
    ]);

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
