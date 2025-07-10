<?php

namespace Drupal\voting_api\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'poll_option_widget' widget.
 *
 * @FieldWidget(
 *   id = "poll_option_widget",
 *   label = @Translation("Poll Option widget"),
 *   field_types = {"poll_option_item"}
 * )
 */
class PollOptionWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element += [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['poll-option-item']],
    ];
    // Ensure each option has a UUID.
    $value = $items[$delta] ?? NULL;
    $uuid = $value && !empty($value->uuid) ? $value->uuid : \Drupal::service('uuid')->generate();
    $element['uuid'] = [
      '#type' => 'hidden',
      '#value' => $uuid,
    ];

    $element['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Option title'),
      '#default_value' => $value ? $value->title : '',
      '#required' => TRUE,
    ];
    $element['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Option description'),
      '#default_value' => $value ? $value->description : '',
    ];
    $element['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Option image'),
      '#default_value' => $value && $value->image ? [$value->image] : [],
      '#upload_location' => 'public://poll_options/',
      '#required' => FALSE,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$item) {
      // Preserve or generate UUID per item.
      $item['uuid'] = !empty($item['uuid']) ? $item['uuid'] : \Drupal::service('uuid')->generate();
      // Convert file array to single fid.
      $item['image'] = !empty($item['image']) ? reset($item['image']) : NULL;
    }
    return $values;
  }
}
