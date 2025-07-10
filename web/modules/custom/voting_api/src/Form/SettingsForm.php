<?php

namespace Drupal\voting_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for Voting API.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'voting_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['voting_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('voting_api.settings');
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable voting system'),
      '#description' => $this->t('Check to allow users to vote.'),
      '#default_value' => $config->get('enabled'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('voting_api.settings')
      ->set('enabled', $form_state->getValue('enabled'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
