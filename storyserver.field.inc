<?php
/**
 * @file
 * StoryServer Story field.
 */

/**
 * @defgroup storyserver: Field Types API
 * @ingroup StoryServer
 */

/**
 * Helper method to scan the StoryServer module themes directory
 * and return a list of theme names.
 * Note: Directory names must be snake_case.
 * @return array
 */
function storyserver_theme_names() {
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
 * Implements hook_field_info().
 * @return array
 */
function storyserver_field_info() {
  return array(
    // We name our field as the associative name of the array.
    'storyserver_story' => array(
      'label' => t('StoryServer'),
      'description' => t('StoryServer story field.'),
      'default_widget' => 'storyserver_story_widget',
      'default_formatter' => 'storyserver_story',
    )
  );
}

/**
 * Implements hook_field_validate()
 * @param $entity_type
 * @param $entity
 * @param $field
 * @param $instance
 * @param $langcode
 * @param $items
 * @param $errors
 */
function storyserver_field_validate($entity_type, $entity, $field, $instance, $langcode, $items, &$errors) {

  foreach ($items as $delta => $item) {
    if (!empty($item['story_id'])) {
      if (!preg_match('@^[1-9][0-9]{0,9}$@', $item['story_id'])) {
        $errors[$field['field_name']][$langcode][$delta][] = array(
          'error' => 'storyserver_invalid',
          'message' => t('StoryServer story ID must be a number.'),
        );
      }
    }

    if (!empty($item['story_theme'])) {
      if (!preg_match('@^[A-Za-z0-9_]+$@', $item['story_theme'])) {
        $errors[$field['field_name']][$langcode][$delta][] = array(
          'error' => 'storyserver_invalid',
          'message' => t('StoryServer theme must be an alphanumeric.'),
        );
      }
    }
  }
}

/**
 * Implements hook_field_is_empty().
 * @param $item
 * @param $field
 * @return bool
 */
function storyserver_field_is_empty($item, $field) {
  $temp = array_keys($field['columns']);
  $key = array_shift($temp);
  return empty($item[$key]);
}

/**
 * Implements hook_field_widget_info().
 *
 */
function storyserver_field_widget_info() {
  return array(
    'storyserver_story_widget' => array(
      'label' => t('StoryServer Widget'),
      'field types' => array('storyserver_story'),
    )
  );
}


/**
 * StoryServer Widget definition.
 * @param $form
 * @param $form_state
 * @param $field
 * @param $instance
 * @param $langcode
 * @param $items
 * @param $delta
 * @param $element
 * @return array
 */
function storyserver_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {
  global $theme_key;
  $widget = $element;
  $widget['#delta'] = $delta;
  $theme_css = '';
  switch ($instance['widget']['type']) {
    case 'storyserver_story_widget':
      switch($theme_key) {
        case 'seven':
          $theme_css = drupal_get_path('module', 'storyserver') . '/css/story_fields_widget-seven.css';
          break;
        case 'adminimal':
          $theme_css = drupal_get_path('module', 'storyserver') . '/css/story_fields_widget-adminimal.css';
          break;
        default:
          $theme_css = drupal_get_path('module', 'storyserver') . '/css/story_fields_widget-seven.css';
      }

      // Make this a fieldset with the two select fields.
      $widget += array(
        '#type' => 'fieldset',

        // #delta is set so that the validation function will be able
        // to access external value information which otherwise would be
        // unavailable.
        '#delta' => $delta,

        '#attached' => array(
          'css' => array(
            array(
              'data' => drupal_get_path('module', 'storyserver') . '/select2/select2.min.css',
              'type' => 'file',
            ),
            array(
              'data' => $theme_css,
              'type' => 'file'
            ),
          ),
          'js' => array(
            array(
              'data' => drupal_get_path('module', 'storyserver') . '/select2/select2.min.js',
              'type' => 'file',
            ),
            array(
              'data' => drupal_get_path('module', 'storyserver') . '/js/storyserver_select.js',
              'type' => 'file',
            ),
          )
        )
      );

      $story_name = isset($items[$delta]['story_name']) ? $items[$delta]['story_name'] : '';
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

      $story_id = isset($items[$delta]['story_id']) ? $items[$delta]['story_id'] : '';
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

      $story_theme = isset($items[$delta]['story_theme']) ? $items[$delta]['story_theme'] : '';
      $widget['story_theme'] = array(
        '#type' => 'select',
        '#title' => t('Theme'),
        '#options' => storyserver_theme_names(),
        '#default_value' => $story_theme,
        '#attributes' => array('class' => array('storyserver-theme')),
        '#description' => t('The StoryServer theme used to display this story.', array('@theme' => 'Story Theme'))
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
      if ($instance['required'] == 1) {
        $widget['story_id']['#required'] = 1;
        $widget['story_theme']['#required'] = 1;
      }

      break;
  }

  return $widget;
}


/**
 * Hacky implementation to remove a Story from the node.
 * Not sure why the story ID is submitted from the Select2 control after we've
 * called $select.val('').trigger("change"); but it is, so we check here for
 * and empty story_name - and if empty, remove all of the storyserver fields.
 * @see storyserver/scripts/storyserver_select.js
 * @param $entity_type
 * @param $entity
 * @param $form
 * @param $form_state
 */
function storyserver_field_attach_submit($entity_type, $entity, $form, &$form_state) {

  list(, , $bundle) = entity_extract_ids($entity_type, $entity); //We don't care about IDs, and this will give us the bundle name/content type.
  $fields_info = field_info_instances($entity_type, $bundle);
  $field = '';
  foreach ($fields_info as $field_name => $value) {
    $type = field_info_field($field_name)['type'];
    if($type == "storyserver_story") {
      $field = $field_name;
      break;
    }
  }

  // If the story_name is missing, then clear the other fields
  // effectively removing the story from this entity/node.
  if (isset($form_state["field"][$field])) {
    if (empty($entity->{$field}[LANGUAGE_NONE]["0"]['story_name'])) {
      unset($entity->{$field}[LANGUAGE_NONE]["0"]['story_id']);
      unset($entity->{$field}[LANGUAGE_NONE]["0"]['story_theme']);
      unset($entity->{$field}[LANGUAGE_NONE]["0"]['story_name']);
    }
  }
}


/**
 * Implements hook_field_formatter_info().
 *
 * @see storyserver_field_formatter_view()
 */
function storyserver_field_formatter_info() {
  return array(
    'storyserver_story' => array(
      'label' => t('Story Formatter'),
      'field types' => array('storyserver_story'),
    ),
  );
}

/**
 * Implements hook_field_formatter_view().
 * @see storyserver_field_formatter_info()
 * @param $entity_type
 * @param $entity
 * @param $field
 * @param $instance
 * @param $langcode
 * @param $items
 * @param $display
 * @return array
 */
function storyserver_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {

  $element = array();

  switch ($display['type']) {
    case 'storyserver_story':

      if(!empty($items)) {
        $story_id = $items['0']['story_id'];
        $story_theme = $items['0']['story_theme'] ?: 'default';

        if(!empty($story_id) && !empty($story_theme)) {

          $themePath = drupal_get_path('module', 'storyserver') . '/themes/'. $story_theme;

          $settings = parse_ini_file($themePath . '/story.ini');
          if(!empty($settings) && !empty($settings['formats'])) {

            $css = $settings['stylesheets'];
            if(!empty($css)) {
              $arrlength = count($css);
              for($x = 0; $x < $arrlength; $x++) {
                $css[$x] = $themePath . '/' . $css[$x];
              }
            }

            $scripts = $settings['scripts'];
            if(!empty($scripts)) {
              $arrlength = count($scripts);
              for($x = 0; $x < $arrlength; $x++) {
                $scripts[$x] = $themePath . '/' . $scripts[$x];
              }
            }

            // Make our API call and retrieve the StoryServer metadata for the story.
            $story = storyserver_get_story($story_id, $settings['formats']);

            if($story) {
              // Set the theme and template data in the render array.
              // See storyserver.module hook_theme_registry_alter() hook
              $element[0] = array(
                '#theme' => 'storyserver_theme_' . $story_theme,
                '#story_id' => $story_id,
                '#story_theme' => $story_theme,
                '#story' => $story['data'],
                '#safeJson' => $story['safeJson'],
                '#appServer' =>  file_create_url($themePath),
                '#attached' => array(
                  'css' => $css,
                  'js' => $scripts
                ),
              );
            }
          }
        }
      }

      break;
  }

  return $element;
}


/**
 * Implements hook_field_widget_error().
 *
 * hook_field_widget_error() lets us figure out what to do with errors
 * we might have generated in hook_field_validate(). Generally, we'll just
 * call form_error().
 *
 * @see storyserver_field_validate()
 * @see form_error()
 */
function storyserver_field_widget_error($element, $error, $form, &$form_state) {
  switch ($error['error']) {
    case 'storyserver_invalid':
      form_error($element, $error['message']);
      break;
  }
}

/**
 * Restrict the number of storyserver_story field instances per node to 1
 * @param $form
 * @param $form_state
 * @param $form_id
 */
function storyserver_form_field_ui_field_edit_form_alter(&$form, &$form_state, $form_id) {
  if ($form['#field']['type'] == 'storyserver_story') {
    $form['field']['cardinality']['#options'] = array(1);
    $form['field']['cardinality']['#default_value'] = 1;
    $form['field']['cardinality']['#disabled'] = TRUE;
  }
}

/**
 * @} End of "defgroup storyserver".
 */
