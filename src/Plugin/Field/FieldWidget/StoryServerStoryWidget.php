<?php /**
 * @file
 * Contains \Drupal\storyserver\Plugin\Field\FieldWidget\StoryServerStoryWidget
 */

namespace Drupal\storyserver\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *  id = "storyserver_story_widget",
 *  label = @Translation("StoryServer Widget"),
 *  field_types = {"storyserver_story"}
 * )
 */
class StoryServerStoryWidget extends WidgetBase {

  /**
   * Helper method to return the list of StoryServer themes.
   * Theme directory names must be in snake_case.
   * @return array
   */
  private function storyserver_theme_names() {
    $theme_names = array();
    $module_path = drupal_get_path('module', 'storyserver');
    $directories = glob($module_path . '/themes/*', GLOB_ONLYDIR);
    if(!empty($directories)) {
      foreach ($directories as $directory) {
        $name = basename($directory);
        $title = ucwords(str_replace("_", " ", $name));
        $theme_names[$name] = $title;
      }
    }
    return $theme_names;
  }


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $widget = $element;

    //This is a complex field - getValue will return an array of the columns.
    $storyServerItem = $items->get($delta);
    $values = isset($storyServerItem) ? $storyServerItem->getValue() : array();

    // Make this a fieldset with the two select fields and one hidden field.
    $widget += array(
      '#type' => 'fieldset',

      // #delta is set so that the validation function will be able
      // to access external value information which otherwise would be
      // unavailable.
      '#delta' => $delta,

      '#attached' => array(
        'library' =>  array(
          'storyserver/storyserver-widget'
        ),
      )
    );

    $story_name = isset($values['story_name']) ? $values['story_name'] : '';
    $widget['story_name'] = array(
      '#type' => 'hidden',
      '#title' => 'Story Name',
      '#validated' => TRUE,
      '#default_value' => $story_name,
      '#size' => 128,
      '#maxlength' => 128,
      '#attributes' => array('class' => array('storyserver-name')),
      '#description' => t('The StoryServer Story Name.')
    );

    $story_id = isset($values['story_id']) ? $values['story_id'] : '';
    $widget['story_id'] = array(
      '#type' => 'select',
      '#title' => t('Story'),
      '#default_value' => $story_id,
      '#validated' => TRUE,
      '#multiple' => 0,
      '#options' => array(
        $story_id => $story_name
      ),
      '#attributes' => array('class' => array('storyserver-id')),
      '#description' => t('The StoryServer Story to display in this post.')
    );

    $story_theme = isset($values['story_theme']) ? $values['story_theme'] : '';
    $widget['story_theme'] = array(
      '#type' => 'select',
      '#title' => t('Theme'),
      '#options' => $this->storyserver_theme_names(),
      '#default_value' => $story_theme,
      '#attributes' => array('class' => array('storyserver-theme')),
      '#description' => t('The StoryServer theme used to display this story', array('@theme' => 'Story Theme'))
//        '#prefix' => '<div class="storyserver-story-field storyserver-story-theme">',
//        '#suffix' => '</div>',
    );

    // Add a remove story button
    if(!empty($story_id)) {
      $widget['story_clear'] = array(
        '#type' => 'button',
        '#value' => t('Remove'),
        '#executes_submit_callback' => FALSE,
        '#attributes' => array('class' => array('storyserver-remove-story')),
      );
    }

    // Since Form API doesn't allow a fieldset to be required, we
    // have to require each field element individually.

    if ($element['#required'] == 1) {
      $widget['story_id']['#required'] = 1;
      $widget['story_theme']['#required'] = 1;
    }

    return $widget;
  }

}
