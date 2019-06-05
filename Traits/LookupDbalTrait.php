<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Doctrine\ORM\EntityRepository;

trait LookupDbalTrait
{
    /**
     * @param mixed[] $criteria Field criteria.
     * @param mixed[] $sort     Optional sort.
     * @return object|null
     */
    public function getOneBy(array $criteria, array $sort = []): ?object
    {
        if (!\in_array(EntityRepository::class, \class_parents($this))) {
            throw new \LogicException('This trait can only be used with Doctrine EntityRepository');
        }
        
        return parent::findOneBy($criteria, $sort);
    }

    /**
     * @param mixed[]  $criteria Field criteria.
     * @param mixed[]  $sort     Optional sort.
     * @param int|null $limit    Limit.
     * @param int|null $offset   Offset.
     * @return object[]
     */
    public function getManyBy(array $criteria, array $sort = [], ?int $limit = null, ?int $offset = null): array
    {
        if (!\in_array(EntityRepository::class, \class_parents($this))) {
            throw new \LogicException('This trait can only be used with Doctrine EntityRepository');
        }
        
        return parent::findBy($criteria, $sort, $limit, $offset);
    }
}
