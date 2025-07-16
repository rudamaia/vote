<?php

namespace Drupal\voting_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\voting_api\Entity\PollQuestion;

/**
 * Form controller for voting on a PollQuestion.
 */
class PollVoteForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'poll_vote_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, PollQuestion $poll_question = NULL) {
    // Build radio options from the question's options field.
    $options = [];
    foreach ($poll_question->get('options') as $item) {
      $options[$item->uuid] = $item->title;
    }

    $form['option'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose an option'),
      '#options' => $options,
      '#required' => TRUE,
    ];

    // Hidden field to pass along the question ID.
    $form['poll_question'] = [
      '#type' => 'hidden',
      '#value' => $poll_question->id(),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Vote'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $option_uuid = $form_state->getValue('option');
    $question_id = $form_state->getValue('poll_question');

    // Create and save a new PollVote entity.
    $vote = \Drupal::entityTypeManager()
      ->getStorage('poll_vote')
      ->create([
        'question_id' => $question_id,
        'option_uuid' => $option_uuid,
      ]);
    $vote->save();

    $this->messenger()->addStatus($this->t('Your vote has been recorded.'));

    // Redirect back to the question's canonical page.
    $form_state->setRedirect('entity.poll_question.canonical', [
      'poll_question' => $question_id,
    ]);
  }
}
