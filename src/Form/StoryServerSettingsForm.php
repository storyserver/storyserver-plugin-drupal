<?php
/**
 * @file
 * Contains \Drupal\storyserver\Form\StoryServerSettingsForm.
 */

namespace Drupal\storyserver\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Class StoryServerSettingsForm
 * @package Drupal\storyserver\Controller
 * Configure storyserver settings for this site.
 */
class StoryServerSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'storyserver_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('storyserver.settings');

    $form['storyserver'] = array(
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#title' => t("StoryServer API settings"),

      'keyId' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Key ID'),
        '#default_value' => $config->get('storyserver_key_id'),
        '#description' => $this->t('This is the Key ID used to communicate securely with the StoryServer servers. Your Key ID does not have to be kept secret, although you should keep your Secret Key safe. You can create a Key ID and Secret Key pair from the StoryServer dashboard at https://storyserver.io'),
        '#required' => TRUE,
        '#size' => 30,
        '#maxlength' => 24,
      ),

      'secretKey' => array(
        '#type' => 'textfield',
        '#title' => $this->t("Secret Key"),
        '#default_value' => $config->get('storyserver_secret_key'),
        '#description' => $this->t('This is the secret key used to communicate securely with the StoryServer servers. Please keep this key safe. You can create a Key ID and Secret Key pair from the StoryServer dashboard at https://storyserver.io'),
        '#required' => TRUE,
        '#size' => 66,
        '#maxlength' => 64,
      ),

      'apiServer' => array(
        '#type' => 'textfield',
        '#title' => $this->t("StoryServer Server"),
        '#default_value' => $config->get('storyserver_api_server'),
        '#description' => $this->t('The location of the StoryServer server used to deliver story metadata to this Web site. Unless you\'ve been specifically advised to do so, you won\'t normally need to change this setting.'),
        '#required' => TRUE,
        '#size' => 40,
        '#maxlength' => 36,
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('storyserver.settings')
      ->set('storyserver_key_id', $form_state->getValue('keyId'))
      ->set('storyserver_secret_key', $form_state->getValue('secretKey'))
      ->set('storyserver_api_server', $form_state->getValue('apiServer'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'storyserver.settings',
    ];
  }
}
?>