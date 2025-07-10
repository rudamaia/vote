<?php

namespace Drupal\voting_api\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Vote entity.
 */
class VoteAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view poll_vote entities');
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete poll_vote entities');
    }
    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $config = \Drupal::config('voting_api.settings');
    // Prevent votes when the voting API is disabled.
    if (!$config->get('enabled')) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowedIfHasPermission($account, 'create poll_vote entities');
  }
}
