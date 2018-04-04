<?php

/**
 * @file
 * Contains \Drupal\madness\MadnessEventSubscriber.
 */

namespace Drupal\madness\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MadnessEventSubscriber.
 *
 * @package Drupal\madness
 */
class MadnessEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest', 20];
    return $events;
  }

  /**
   * Code that should be triggered on event specified 
   */
  public function onRequest(GetResponseEvent $event) {
    // @TODO Fix this to run only once per page load.

    // Don't bother running this on /admin pages.
    if (strpos($event->getRequest()->getPathInfo(), '/admin') === FALSE) {
      // Check config to see if this behavior is desired.
      if ((int) \Drupal::config('madness.settings')->get('increase_on_every_request')) {
        $users = \Drupal::service('madness.levels')->increaseMadness(2);
      }
    }
  }

}