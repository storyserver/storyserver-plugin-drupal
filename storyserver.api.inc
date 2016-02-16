<?php

use StoryServer\Client;

/**
 * Story names lookup field module during node create/edit.
 */
function storyserver_get_story_names() {

  $keyId     = variable_get('storyserver_key_id', '');
  $secretKey = variable_get('storyserver_secret_key', '');
  $apiServer = variable_get('storyserver_api_server', '');

  $client = new Client([
    'storyServer' => $apiServer,
    'keyId' => $keyId,
    'secretKey' => $secretKey,
    'formats' => '',
    'appServer'=> ''
  ]);

  if (!empty($_GET['storyname'])) {
    $q = $_GET['storyname'];
  } else {
    $q = '';
  }

  $result = null;
  try {
    $result = $client->getStoryNames($q);
  } catch (Exception $e) {
    watchdog('storyserver', $e->getMessage(), $variables = array(), $severity = WATCHDOG_ERROR, $link = NULL);
  }

  if($result) {
    drupal_json_output($result['data']);
  }
  exit();
}

/**
 * Get a story
 * @param $id
 * @param $formats
 * @return array
 */
function storyserver_get_story($id, $formats) {

  $keyId     = variable_get('storyserver_key_id', '');
  $secretKey = variable_get('storyserver_secret_key', '');
  $apiServer = variable_get('storyserver_api_server', '');

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
  } catch (Exception $e) {
    watchdog('storyserver', $e->getMessage(), $variables = array(), $severity = WATCHDOG_ERROR, $link = NULL);
  }

  return $result;
}