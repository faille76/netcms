<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Storage\Mysql;

use App\Shared\Domain\Criteria;
use Doctrine\DBAL\Query\QueryBuilder;

trait ApplyCriteriaTrait
{
    protected function applyCriteria(QueryBuilder $qb, ?Criteria $criteria): void
    {
        if ($criteria === null) {
            return;
        }

        if ($criteria->getLimit() !== null) {
            $qb->setMaxResults($criteria->getLimit());
        }

        if ($criteria->getOffset() !== null) {
            $qb->setFirstResult($criteria->getOffset());
        }

        if ($criteria->getOrdersBy() !== null) {
            foreach ($criteria->getOrdersBy() as $key => $order) {
                $qb->orderBy($key, $order);
            }
        }
    }
}
