<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Event;

/**
 * Class Event
 *
 * @package Cstea\ApiBundle
 */
abstract class Event extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * Get event name.
     *
     * @return string
     */
    public static function getName(): string
    {
        return \str_replace('\\', '.', static::class);
    }
}
