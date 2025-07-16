<?php

namespace Drupal\voting_api\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the PollQuestion entity.
 *
 * @ContentEntityType(
 *   id = "poll_question",
 *   label = @Translation("Poll Question"),
 *   base_table = "poll_question",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title"
 *   },
 *   handlers = {
 *     "access" = "Drupal\voting_api\Access\PollQuestionAccessControlHandler",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\voting_api\PollQuestionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\voting_api\Form\PollQuestionForm",
 *       "edit" = "Drupal\voting_api\Form\PollQuestionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   links = {
 *     "canonical"  = "/poll_question/{poll_question}",
 *     "collection" = "/admin/content/poll_question",
 *     "add-form"   = "/admin/content/poll_question/add",
 *     "edit-form"  = "/admin/content/poll_question/{poll_question}/edit",
 *     "delete-form"= "/admin/content/poll_question/{poll_question}/delete"
 *   }
 * )
 */
class PollQuestion extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE);

    // PollOption as options for this question.
    $fields['options'] = BaseFieldDefinition::create('poll_option_item')
      ->setLabel(t('Options'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'poll_option_widget',
        'weight' => 5,
      ])
      ->setDisplayOptions('view', [
        'type' => 'poll_option_default',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['show_results'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Show Results'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
      ])
      ->setDisplayConfigurable('form', TRUE);

    // Total votes received.
    $fields['total_votes'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Total votes'))
      ->setDescription(t('Total number of votes cast for this question.'))
      ->setDefaultValue(0)
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'number_integer',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // JSON map of option UUID to vote count.
    $fields['option_counts'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Option counts'))
      ->setDescription(t('JSON map of option UUID to vote count.'))
      ->setDefaultValue('{}')
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // JSON map of option UUID to vote percentage.
    $fields['option_percentages'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Option percentages'))
      ->setDescription(t('JSON map of option UUID to vote percentage.'))
      ->setDefaultValue('{}')
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 12,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
