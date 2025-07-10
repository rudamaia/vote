<?php

namespace Drupal\voting_api\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for PollQuestion add/edit forms.
 */
class PollQuestionForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    // Standard Drupal status message.
    $this->messenger()->addStatus($this->t('Poll question %label has been saved.', [
      '%label' => $entity->label(),
    ]));

    return $status;
  }
}
