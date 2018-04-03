<?php

namespace Drupal\madness\Commands;

use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class MadnessCommands extends DrushCommands {

  /**
   * Create test users for the Madness module.
   *
   * @param array $options
   *   An associative array of options whose values come from cli, aliases, config, etc.
   * 
   * @option count
   *   The number of users to create (max 11)
   * @usage drush mcu
   *   Generate Madness test users (11 by defaut).
   * @usage drush mec --count=6
   *   Generate 6 Madness test users
   *
   * @command madness:create-users
   * @aliases mcu,madness-create-users
   */
  public function createUsers(array $options = ['count' => 11]) {
    $count = $options['count'];

    $users = $this->getUserData($count);

    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    foreach ($users as $user_info) {
      $user = User::create();

      $user->setUsername($user_info['name']);
      $user->setPassword($user_info['pass']);
      $user->setEmail($user_info['mail']);
      // $user->addRole('authenticated');
      $user->enforceIsNew();

      $user->set("init", 'user@example.com');
      $user->set("langcode", $lang);
      $user->set("preferred_langcode", $lang);
      $user->set("preferred_admin_langcode", $lang);
      $user->activate();

      $result = $user->save();
    }

    $this->output()->writeln(dt("[<info>ok</info>] I created <comment>@count</comment> new @users for the <error>Great Old Ones</error> to devour!", [
      '@count' => $count,
      '@users' => \Drupal::translation()->formatPlural($count, 'user', 'users')
    ]));
  }

  /**
   * Get user info for random test users.
   *
   * @param int $count
   *   The number of users to return.
   *
   * @return array
   *   A randomized array of user info.
   */
  private function getUserData($count) {
    $users = [
      [
        'name' => 'Herbert West',
        'mail' => 'hwest@reanimator.com',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Dr. Henry Armitage',
        'mail' => 'henry.armitage@miskatonic.edu',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Francis Wayland Thurston',
        'mail' => 'waythurst28@compuserv.com',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Randolph Carter',
        'mail' => 'totallynot@hplovecraft.com',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Joe Czanek',
        'mail' => 'the@terribleoldman.com',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Charles Dexter Ward',
        'mail' => 'charlie@theresurrected.com',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Gabriella Maldonado',
        'mail' => 'gabi84@hundredthousand.net',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Dana Anne Shirefield',
        'mail' => 'danas@newtowne.edu',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Professor Alice Derleth',
        'mail' => 'alice.derleth@miskatonic.edu',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Eliza Orne',
        'mail' => 'elizao@innsmouth.org',
        'pass' => $this->generateRandomPassword(),
      ],
      [
        'name' => 'Doctor Juan Muñoz',
        'mail' => 'dr.chill@yahoo.com',
        'pass' => $this->generateRandomPassword(),
      ],
    ];

    shuffle($users);

    return array_slice($users, 0, $count);
  }

  /**
   * Create a random (16-character?) light-weight password.
   *
   * @param int $length
   *   The number of characters in the password.
   *
   * @return string
   *   The random password.
   */
  private function generateRandomPassword($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
      $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
  }

}
