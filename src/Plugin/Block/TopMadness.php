<?php

namespace Drupal\madness\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a block to display highest insane users.
 *
 * @Block(
 *   id = "top_madness",
 *   admin_label = @Translation("Top Madness Levels"),
 *   category = @Translation("Madness"),
 * )
 */
class TopMadness extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Query for user entities sorted by the madness_level field.
    $query = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('uid', 1, '>')
      ->condition('madness_level', 0, '>')
      ->sort('madness_level', 'DESC');

    // Get User IDs.
    $users = User::loadMultiple($query->execute());

    // Load User entities and get the values we want to display.
    $user_data = [];
    foreach ($users as $uid => $user) {
      $user_data[$uid] = [
        'name' => $user->getDisplayName(),
        'madness_level' => $user->get('madness_level')->value,
      ];
    }

    // Return a tabular renderable array of user madness levels.
    return array(
      '#type'   => 'table',
      '#header' => ['User', 'Madness level'],
      '#rows'   => $user_data,
      '#empty'  => $this->t('No one is mad (yet!)'),
    );
  }

}
