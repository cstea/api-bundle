<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Cstea\ApiBundle\Event\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Trait EventAware
 * Provides classes the trait of firing events.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait EventAware
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * Setter injection method for the Event dispatcher.
     *
     * @required
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher.
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Fires an event.
     *
     * @param Event $event Event to trigger.
     */
    public function triggerEvent(Event $event): void
    {
        $this->eventDispatcher->dispatch($event::getName(), $event);
    }
}
