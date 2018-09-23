<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerAware
 * Provides classes the trait of logging.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait LoggerAware
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * Gets logger.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Setter injection method for the LoggerInterface.
     *
     * @required
     * @param LoggerInterface $logger Logger.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
