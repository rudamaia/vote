<?php

namespace Drupal\voting_api;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a list builder for PollQuestion entities.
 */
class PollQuestionListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['total_votes'] = $this->t('Total votes');
    $header['stats'] = $this->t('Options');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\voting_api\Entity\PollQuestion $entity */
    // Link the title to the entity's canonical view page.
    $row['title'] = Link::createFromRoute(
      $entity->label(),
      'entity.poll_question.canonical',
      ['poll_question' => $entity->id()]
    );
    $row['total_votes'] = $entity->get('total_votes')->value;

    // Decode stored option counts and percentages.
    $counts = json_decode($entity->get('option_counts')->value, TRUE);
    $percents = json_decode($entity->get('option_percentages')->value, TRUE);

    // Build per-option progress gauges and labels.
    $items = [];
    $total = $row['total_votes'];
    foreach ($entity->get('options') as $item) {
      $name = $item->title;
      $uuid = $item->uuid;
      $value = isset($counts[$uuid]) ? $counts[$uuid] : 0;
      $percent = $total ? round(($value / $total) * 100) : 0;
      // Render an HTML5 progress bar.
      $items[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['poll-gauge-item']],
        'bar' => [
          '#type' => 'html_tag',
          '#tag' => 'progress',
          '#attributes' => [
            'max' => 100,
            'value' => $percent,
          ],
        ],
        'label' => [
          '#markup' => ' ' . $name . ' (' . $percent . '%)',
        ],
      ];
    }
    if (!empty($items)) {
      // Render gauges inside a container without list bullets.
      $row['stats'] = [
        'data' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['poll-gauge-list']],
          // Attach each gauge item (already a render array).
          'gauge_items' => $items,
        ],
      ];
    } else {
      $row['stats'] = [
        'data' => $this->t('—'),
      ];
    }

    // Append default operations.
    return $row + parent::buildRow($entity);
  }
}
