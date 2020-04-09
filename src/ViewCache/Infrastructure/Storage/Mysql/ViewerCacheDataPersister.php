<?php

declare(strict_types=1);

namespace App\ViewCache\Infrastructure\Storage\Mysql;

use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;
use App\ViewCache\Domain\ViewerCacheDataPersisterInterface;

final class ViewerCacheDataPersister extends AbstractDataPersister implements ViewerCacheDataPersisterInterface
{
    public function addViewer(int $objectId, int $type, string $ip): int
    {
        $this->connection->insert('view', [
            'ip' => $ip,
            'article_id' => $objectId,
            'type' => $type,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function addUserViewed(int $objectId, int $type, int $userId): int
    {
        $this->connection->insert('view_users', [
            'user_id' => $userId,
            'article_id' => $objectId,
            'type' => $type,
        ]);

        return (int) $this->connection->lastInsertId();
    }
}
