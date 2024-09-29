<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Repository;

/**
 * Interface ReadOnly.
 * Repository interface for data sources that require read-only access.
 *
 * @package Cstea\ApiBundle\Repository
 */
interface ReadOnlyRepository
{
    /**
     * @param mixed[]  $criteria Field criteria.
     * @param string[] $sort     Optional sort.
     * @return \object|null
     */
    public function getOneBy(array $criteria, array $sort = []): ?object;

    /**
     * @param mixed[]  $criteria Field criteria.
     * @param string[] $sort     Sort options.
     * @param int|null $limit    Limit.
     * @param int|null $offset   Offset.
     * @return \object[]
     */
    public function getManyBy(array $criteria, array $sort = [], ?int $limit = null, ?int $offset = null): array;
}
