<?php

namespace Drupal\madness\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

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

  // Set a default value to use for number of users to display.
  protected $user_count = 5;

  // Set a default value for linking to user pages or not.
  protected $link = FALSE;

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $user_count = $config['user_count'] ?: $this->user_count;

    // Get user entities from the Madness service.
    $users = \Drupal::service('madness.levels')->getUsers($user_count);

    // Load User entities and get the values we want to display.
    $user_data = [];
    foreach ($users as $uid => $user) {
      // Link to the user page if the block is configured to do so.
      $username = $config['link'] ? Link::fromTextAndUrl($user->getDisplayName(), Url::fromUri('internal:/user/' . $uid)) : $user->getDisplayName();

      // Create the table row array values.
      $user_data[$uid] = [
        'name' => $username,
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

    $count = (int) $config['user_count'];
    $form['user_count'] = [
      '#type' => 'select',
      '#options' => [3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15],
      '#title' => $this->t('Number of users'),
      '#description' => $this->t('How many insane users should this block display?'),
      '#default_value' => $count ?: $this->user_count,
    ];

    $form['link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Link to user?'),
      '#description' => $this->t('Should the user\'s name in the block link to their user page (if users have permission to view them)?'),
      '#default_value' => $config['link'] ?: $this->link,
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
    $this->configuration['link'] = $values['link'];
  }

}
