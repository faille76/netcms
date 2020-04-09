<?php

declare(strict_types=1);

namespace App\ViewCache\Infrastructure\Storage\Mysql;

use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\ViewCache\Domain\ViewerCacheDataProviderInterface;

final class ViewerCacheDataProvider extends AbstractDataProvider implements ViewerCacheDataProviderInterface
{
    public function hasViewed(int $objectId, int $type, string $ip): bool
    {
        $qb = $this->createQueryBuilder()
            ->from('view', 'view')
            ->select('count(*)')
            ->andWhere('view.ip = :ip')
            ->andWhere('view.type = :type')
            ->andWhere('view.article_id = :objectId')
            ->setParameter('objectId', $objectId, 'integer')
            ->setParameter('type', $type, 'integer')
            ->setParameter('ip', $ip, 'string')
        ;

        return $this->fetchIntColumn($qb) > 0;
    }

    public function hasUserViewed(int $objectId, int $type, int $userId): bool
    {
        $qb = $this->createQueryBuilder()
            ->from('view_users', 'view')
            ->select('count(*)')
            ->andWhere('view.user_id = :userId')
            ->andWhere('view.type = :type')
            ->andWhere('view.article_id = :objectId')
            ->setParameter('objectId', $objectId, 'integer')
            ->setParameter('type', $type, 'integer')
            ->setParameter('userId', $userId, 'integer')
        ;

        return $this->fetchIntColumn($qb) > 0;
    }
}
