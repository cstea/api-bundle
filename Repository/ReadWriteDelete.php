<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Repository;

/**
 * Interface ReadWriteDelete
 * Repository interface for data sources that require read, save and delete access.
 * 
 * @package Cstea\ApiBundle\Repository
 */
interface ReadWriteDelete extends \Cstea\ApiBundle\Repository\ReadWrite
{
    /**
     * @param \object $entity Entity to delete.
     */
    public function delete(object $entity): void;
}
