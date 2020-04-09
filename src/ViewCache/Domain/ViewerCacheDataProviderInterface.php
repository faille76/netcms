<?php

declare(strict_types=1);

namespace App\ViewCache\Domain;

interface ViewerCacheDataProviderInterface
{
    public function hasViewed(int $objectId, int $type, string $ip): bool;

    public function hasUserViewed(int $objectId, int $type, int $userId): bool;
}
