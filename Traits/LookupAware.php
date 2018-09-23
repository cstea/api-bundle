<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Cstea\ApiBundle\Repository\ReadOnly;

/**
 * Trait LookupAware
 * Provides classes the trait of data fetching from repositories.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait LookupAware
{
    /**
     * Wrapper for getting a single record from a repository.
     *
     * @param ReadOnly $repository Repository.
     * @param mixed[]  $criteria   Field criteria.
     * @param string[] $sort       Optional sort.
     * @return \object
     * @throws \Cstea\ApiBundle\Exception\RecordLookupException Lookup error.
     * @throws \Cstea\ApiBundle\Exception\RecordNotFoundException No results.
     */
    protected function getOne(ReadOnly $repository, array $criteria = [], array $sort = []): object
    {
        try {
            $entity = $repository->getOneBy($criteria, $sort);
        } catch (\Throwable $exception) {
            throw new \Cstea\ApiBundle\Exception\RecordLookupException($exception);
        }
        if (!$entity) {
            throw new \Cstea\ApiBundle\Exception\RecordNotFoundException();
        }

        return $entity;
    }

    /**
     * Wrapper function for getting a collection of records from a repository.
     *
     * @param ReadOnly $repository Repository.
     * @param mixed[]  $criteria   Field criteria.
     * @param string[] $sort       Order.
     * @param int|null $limit      Limit.
     * @param int|null $offset     Offset.
     * @return \object[]
     * @throws \Cstea\ApiBundle\Exception\RecordLookupException Record lookup.
     */
    protected function getMany(
        ReadOnly $repository,
        array $criteria = [],
        array $sort = [],
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            return $repository->getManyBy($criteria, $sort, $limit, $offset);
        } catch (\Throwable $exception) {
            throw new \Cstea\ApiBundle\Exception\RecordLookupException($exception);
        }
    }
}
