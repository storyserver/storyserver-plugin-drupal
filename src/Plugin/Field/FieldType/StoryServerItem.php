<?php

/**
 * @file
 * Contains Drupal\storyserver\Plugin\Field\FieldType\StoryServerItem
 */

namespace Drupal\storyserver\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;


/**
 * Plugin implementation of the 'storyserver' field type.
 *
 * @FieldType(
 *   id = "storyserver_story",
 *   label = @Translation("StoryServer"),
 *   module = "storyserver",
 *   description = @Translation("StoryServer Story"),
 *   default_widget = "storyserver_story_widget",
 *   default_formatter = "storyserver_story"
 * )
 */
class StoryServerItem extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'story_id' => array(
          'type' => 'varchar',
          'length' => 14,
          'not null' => FALSE,
        ),
        'story_name' => array(
          'type' => 'varchar',
          'length' => 128,
          'not null' => FALSE,
        ),
        'story_theme' => array(
          'type' => 'varchar',
          'length' => 64,
          'not null' => FALSE,
        ),
      ),
      'indexes' => array(
        'story_id' => array('story_id'),
        'story_name' => array('story_name'),
        'story_theme' => array('story_theme'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('story_id')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['story_id'] = DataDefinition::create('string')
      ->setLabel(t('Story ID'));
    $properties['story_name'] = DataDefinition::create('string')
      ->setLabel(t('Story Name'));
    $properties['story_theme'] = DataDefinition::create('string')
      ->setLabel(t('Story Theme'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'story_id' => array(
        'Length' => array(
          'max' => 14,
          'maxMessage' => t('%name: may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => 14)),
        )
      ),
      'story_name' => array(
        'Length' => array(
          'max' => 128,
          'maxMessage' => t('%name: may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => 128)),
        )
      ),
      'story_theme' => array(
        'Length' => array(
          'max' => 64,
          'maxMessage' => t('%name: may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => 64)),
        )
      ),
    ));
    return $constraints;
  }


  /**
   * Hacky method to check if the story name is empty, and if so, remove the
   * StoryServer story from this entity.
   * @see - storyserver/js/storyserver_select.js for why this is necessary.
   * {@inheritdoc}
   */
  public function preSave() {
    parent::preSave();

    $entity = $this->getEntity();
    $fields = $entity->getFieldDefinitions();
    $field_name = '';

    foreach($fields as $key => $value) {
      if($value->getType() == "storyserver_story") {
        $field_name = $key;
      }
    }

    if(!empty($field_name)) {
      $values = $this->values;
      if(empty($values['story_name'])) {
        $entity->set($field_name, NULL);
      }
    }
  }
}
