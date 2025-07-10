<?php

namespace Drupal\voting_api\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'poll_option_item' field type.
 *
 * @FieldType(
 *   id = "poll_option_item",
 *   label = @Translation("Poll Option"),
 *   description = @Translation("Holds a poll option (title, description, image)."),
 *   category = @Translation("Polling"),
 *   default_widget = "poll_option_widget",
 *   default_formatter = "poll_option_default"
 * )
 */
class PollOptionItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Option title'));
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Option description'));
    $properties['image'] = DataDefinition::create('integer')
      ->setLabel(t('Image file ID'))
      ->setRequired(FALSE);
    // Stable identifier per option.
    $properties['uuid'] = DataDefinition::create('string')
      ->setLabel(t('Option UUID'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'title' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
        ],
        'description' => [
          'type' => 'text',
          'size' => 'medium',
          'not null' => FALSE,
        ],
        'image' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ],
        'uuid' => [
          'type' => 'varchar',
          'length' => 36,
          'not null' => TRUE,
          'default' => '',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('title')->getValue();
    return $value === NULL || $value === '';
  }
}
