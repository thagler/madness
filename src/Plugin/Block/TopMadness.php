<?php

namespace Drupal\madness\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block to display highest insane users.
 *
 * @Block(
 *   id = "top_madness",
 *   admin_label = @Translation("Top Madness Levels"),
 *   category = @Translation("Madness"),
 * )
 */
class TopMadness extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $config['user_count'] = $config['user_count'] ?: 5;

    // Query for user entities sorted by the madness_level field.
    $query = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('uid', 1, '>')
      ->condition('madness_level', 0, '>')
      ->sort('madness_level', 'DESC')
      ->range(0, $config['user_count']);

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

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['user_count'] = [
      '#type' => 'select',
      '#options' => [3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15],
      '#title' => $this->t('Number of users'),
      '#description' => $this->t('How many insane users should this block display?'),
      '#default_value' => isset($config['user_count']) ? $config['user_count'] : '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['user_count'] = $values['user_count'];
  }

}
