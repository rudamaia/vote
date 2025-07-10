<?php

namespace Drupal\voting_api\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\file\Entity\File;

/**
 * Plugin implementation of the 'poll_option_default' formatter.
 *
 * @FieldFormatter(
 *   id = "poll_option_default",
 *   label = @Translation("Default"),
 *   field_types = {"poll_option_item"}
 * )
 */
class PollOptionDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $lines = [];
      $lines[] = $this->t('Title: @title', ['@title' => $item->title]);
      if (!empty($item->description)) {
        $lines[] = $this->t('Description: @desc', ['@desc' => $item->description]);
      }
      if (!empty($item->image) && $file = File::load($item->image)) {
        $elements[$delta]['image'] = [
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $file->getFileUri(),
        ];
      }
      $elements[$delta]['text'] = [
        '#theme' => 'item_list',
        '#items' => $lines,
      ];
    }
    return $elements;
  }
}
