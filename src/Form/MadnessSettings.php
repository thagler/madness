<?php
/**
 * @file
 * Contains Drupal\madness\Form\MadnessSettings.
 */
namespace Drupal\madness\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class MadnessSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'madness.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'madness_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('madness.settings');

    $form['madness_increase'] = [
      '#type' => 'fieldgroup',
      '#title' => $this->t('Increase madness'),
    ];

    $form['madness_increase']['description'] = [
      '#markup' => $this->t('<p>Users can be driven mad from multiple events. Choose the events that you would like to possibly increment each users\'s maddness every time that even occurs.</p>'),
    ];

    $form['madness_increase']['increase_on_every_request'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Every page load'),
      '#description' => $this->t('Cause a few random users to lose sanity on every page load. Warning: This is incredibly insane! (This will not happen on admin pages)'),
      '#default_value' => $config->get('increase_on_every_request'),
    ];

    $form['madness_increase']['increase_on_event_cron'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Cron'),
      '#description' => $this->t('Cause each user to roll a sanity check every time cron runs.'),
      '#default_value' => $config->get('increase_on_event_cron'),
    ];

    $form['madness_increase']['increase_on_event_content'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Content change'),
      '#description' => $this->t('More content means more madness! When content is created or updated, there\'s a chance that each user could be driven mad.'),
      '#default_value' => $config->get('increase_on_event_content'),
    ];

    $form['madness_decrease'] = [
      '#type' => 'fieldgroup',
      '#title' => $this->t('Decrease madness'),
    ];

    $form['madness_decrease']['description'] = [
      '#markup' => $this->t('<p>Occasionally, users can regain a little bit of sanity. That is, if you choose to extend this kindness…</p>'),
    ];

    $form['madness_decrease']['decrease_on_event_content'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Content removal'),
      '#description' => $this->t('Any time content is removed, a user gains a little more sanity.'),
      '#default_value' => $config->get('decrease_on_event_content'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('madness.settings')
      ->set('increase_on_every_request', $form_state->getValue('increase_on_every_request'))
      ->set('increase_on_event_cron', $form_state->getValue('increase_on_event_cron'))
      ->set('increase_on_event_content', $form_state->getValue('increase_on_event_content'))
      ->set('decrease_on_event_content', $form_state->getValue('decrease_on_event_content'))
      ->save();
  }

}
