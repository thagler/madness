<?php

namespace Drupal\madness;

use Drupal\user\Entity\User;

/**
 * Class MadnessLevels.
 *
 * @package Drupal/madness
 */
class MadnessLevels {

  protected $user_count = 5;

  public function increaseMadness($count = 0) {
    $users = $this->getUsers($count, TRUE, FALSE);
    $users_driven_madder = [];

    foreach ($users as $uid => $user) {
      if (!$this->sanityCheck()) {
        $user->set('madness_level', $user->madness_level->value + 1);
        $user->save();
        // @TODO Update block cache.
        $users_driven_madder[$uid] = $user;
      }
    }

    if (count($users_driven_madder) === 0) {
      drupal_set_message(t('Sanity prevails! No users have been driven mad.'), 'status');
    }
    elseif (count($users_driven_madder) === 1) {
      $user_driven_madder = reset($users_driven_madder);
      drupal_set_message(t('User @username has been driven a little more insane.', ['@username' => $user_driven_madder->getDisplayName()]), 'warning');
    }
    else {
      $usernames = [];
      foreach ($users_driven_madder as $uid => $user_driven_madder) {
        $usernames[] = $user_driven_madder->getDisplayName();
      }
      $usernames = implode(', ', $usernames);
      drupal_set_message(t('The following users have been driven a little more insane: @usernames.', ['@usernames' => $usernames]), 'warning');
    }

  }

  public function decreaseMadness() {
    return t('Decrease all the madness (sad).');
  }

  public function getMadnessLevels() {
    return t('Get the levels of madness.');
  }

  public function set($option = NULL, $value = NULL) {
    return t("Set the '$option' option to the value '$value'.");
  }

  public function get($option = NULL, $value = NULL) {
    return t("Get the '$option' option to the value '$value'.");
  }

  public function getUsers($count = NULL, $not_fully_mad = FALSE, $sort = TRUE) {
    // Get the default value for user loading.
    $count = $count ?: $this->user_count;

    // Query for user entities sorted by the madness_level field.
    $query = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('uid', 1, '>')
      ->condition('madness_level', 0, '>');

    // Optionally load only users that aren't driven fully insane.
    if ($not_fully_mad) {
      $query->condition('madness_level', 10, '<');
    }

    if ($sort) {
      $query->sort('madness_level', 'DESC');
    }
    else {
      $query->addTag('sort_by_random');
    }

    // Limit the query.
    if ($count) {
      $query->range(0, $count);
    }

    // Return an array of users loaded by the UIDs above.
    return User::loadMultiple($query->execute());
  }

  private function sanityCheck() {
    // The lower the sanity check value, the easier it is to remain sane.
    $current_sanity_check = 6;
    $sanity_roll = rand(1, 20);
    return $sanity_roll >= $current_sanity_check;
  }

}
