<?php
 
namespace Drupal\madness;
 
use Symfony\Component\EventDispatcher\Event;

/**
 * Madness event.
 */
class MadnessEvent extends Event {
 
  /**
   * @var string
   *   Original message.
   */
  protected $message;
 
  /**
   * Implements __construct().
   *
   * @param string $message
   *   Log a message to indicate any madness changes.
   */
  public function __construct($message) {
    $this->message = $message;
  }

  /**
   * @return string $message
   *   Message set by constructor.
   */
  public function getMessage() {
    return $this->message;
  }
 
}
