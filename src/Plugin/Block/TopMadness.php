<?php

namespace Drupal\madness\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    return array(
      '#markup' => $this->t('Most mad users!'),
    );
  }

}
