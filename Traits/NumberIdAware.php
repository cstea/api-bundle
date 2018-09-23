<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

/**
 * Trait NumberIdAware
 * Defines a numeric identifier for entities.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait NumberIdAware
{
    /** @var int */
    private $id;

    /**
     * Gets id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
