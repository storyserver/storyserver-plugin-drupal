<?php

/**
 * StoryServer module settings form.
 * @return mixed
 */
function storyserver_admin_form() {

  $form['storyserver'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#title' => t("StoryServer API settings"),

    'keyId' => array(
      '#type' => 'textfield',
      '#title' => t("Key ID"),
      '#default_value' => variable_get('storyserver_key_id', ''),
      '#description' => 'This is the Key ID used to communicate securely with the StoryServer servers. Your Key ID does not have to be kept secret, although you should keep your Secret Key safe. You can create a Key ID and Secret Key pair from the StoryServer dashboard at https://storyserver.io',
      '#required' => TRUE,
      '#size' => 30,
      '#maxlength' => 24,
    ),

    'secretKey' => array(
      '#type' => 'textfield',
      '#title' => t("Secret Key"),
      '#default_value' => variable_get('storyserver_secret_key', ''),
      '#description' => 'This is the secret key used to communicate securely with the StoryServer servers. Please keep this key safe. You can create a Key ID and Secret Key pair from the StoryServer dashboard at https://storyserver.io',
      '#required' => TRUE,
      '#size' => 66,
      '#maxlength' => 64,
    ),

    'apiServer' => array(
      '#type' => 'textfield',
      '#title' => t("StoryServer Server"),
      '#default_value' => variable_get('storyserver_api_server', 'https://storyserver.io/api/v1'),
      '#description' => 'The location of the StoryServer server used to deliver story metadata to this Web site. Unless you\'ve been specifically advised to do so, you won\'t normally need to change this setting.',
      '#required' => TRUE,
      '#size' => 40,
      '#maxlength' => 36,
    ),
  );

  $form['save'] = array(
    '#type' => 'submit',
    '#value' => t("Save"),
  );

  $form['#submit'] = array('storyserver_admin_form_submit');

  return $form;
}

/**
 * Save StoryServer module settings form.
 * @param $form
 * @param $edit
 */
function storyserver_admin_form_submit(&$form, &$edit) {
  $values = $edit['values'];

  if ($values['op']) {
    variable_set('storyserver_key_id', $values['keyId']);
    variable_set('storyserver_secret_key', $values['secretKey']);
    variable_set('storyserver_api_server', $values['apiServer']);
    drupal_set_message(t("Your settings have been saved."));
  }
}