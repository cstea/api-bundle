<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Repository;

/**
 * Interface ReadWrite
 * Repository interface for data sources that require read and save access.
 * 
 * @package Cstea\ApiBundle\Repository
 */
interface ReadWriteRepository extends \Cstea\ApiBundle\Repository\ReadOnlyRepository
{
    /**
     * @param \object $entity Entity to save.
     */
    public function save(object $entity): void;
}
