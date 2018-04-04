<?php

/**
 * @file
 * Contains \Drupal\madness\MadnessEventSubscriber.
 */

namespace Drupal\madness;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\madness\MadnessEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class MadnessEventSubscriber.
 *
 * @package Drupal\madness
 */
class MadnessEventSubscriber implements EventSubscriberInterface {

  // Custom event.
  const MADNESS = 'madness.madness_event';

  /**
   * @var EventDispatcherInterface
   *   Dispatcher provided by the factory injected below in the constructor.
   */
  protected $eventDispatcher;
 
  /**
   * @var LoggerChannel
   *   Logger provided by the factory injected below in the constructor.
   */
  protected $logger;

  /**
   * Implements __construct().
   *
   * Dependency injection defined in services.yml.
   */
  public function __construct(EventDispatcherInterface $eventDispatcher, LoggerChannelFactory $loggerFactory) {
    $this->eventDispatcher = $eventDispatcher;
    $this->logger = $loggerFactory->get('madness');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => [['onRequest']],
      MadnessEventSubscriber::MADNESS => [['onMadness']],
    ];
  }

  /**
   * Subscribed event callback: KernelEvents::REQUEST.
   *
   * Look at the request and config to determine if we should increment random
   * users' madness levels.
   *
   * @param GetResponseEvent $event
   *   An initially empty response event.
   */
  public function onRequest(GetResponseEvent $event) {
    // Determine if this is not an admin page, and only for master GET requests.
    if (
        $event->getRequest()->isMethod('GET') &&
        $event->isMasterRequest() &&
        strpos($event->getRequest()->getPathInfo(), '/admin') === FALSE
      ) {
      // Dispatch *another* event to manage the madness.
      $this->eventDispatcher->dispatch(MadnessEventSubscriber::MADNESS, new MadnessEvent());
    }
  }

  /**
   * Subscribed event callback: MadnessEventSubscriber::MADNESS.
   *
   * Log the message on the event.
   *
   * @param MadnessEvent $event
   *   An example event, dispatched by the previous method.
   */
  public function onMadness(MadnessEvent $event) {
    // Check config to see if this behavior is desired.
    if ((int) \Drupal::config('madness.settings')->get('increase_on_every_request')) {
      $users = \Drupal::service('madness.levels')->increaseMadness(2);
    }
  }

}
