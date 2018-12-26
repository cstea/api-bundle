<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

/**
 * Trait HashIdAware
 * Defines a string identifier for entities.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait HashIdAware
{
    /** @var string */
    protected $id;
    
    /**
     * Gets id.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
