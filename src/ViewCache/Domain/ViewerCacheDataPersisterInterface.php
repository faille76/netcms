<?php

declare(strict_types=1);

namespace App\ViewCache\Domain;

interface ViewerCacheDataPersisterInterface
{
    public function addViewer(int $objectId, int $type, string $ip): int;

    public function addUserViewed(int $objectId, int $type, int $userId): int;
}
