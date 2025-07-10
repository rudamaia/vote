<?php

namespace Drupal\voting_api\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Vote entity.
 *
 * @ContentEntityType(
 *   id = "poll_vote",
 *   label = @Translation("Poll Vote"),
 *   base_table = "poll_vote",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   handlers = {
 *     "access" = "Drupal\voting_api\Access\VoteAccessControlHandler"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/poll_vote/{poll_vote}"
 *   }
 * )
 */
class Vote extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['question_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Poll Question'))
      ->setSetting('target_type', 'poll_question')
      ->setRequired(TRUE);

    $fields['option_uuid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Option UUID'))
      ->setDescription(t('The UUID of the selected poll option.'))
      ->setSettings(['max_length' => 36])
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'));

    return $fields;
  }
}
