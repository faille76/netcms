<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Provider;

use App\Gallery\Domain\Album;
use App\Gallery\Domain\AlbumCollection;
use App\Shared\Domain\Criteria;

interface AlbumDataProviderInterface
{
    public function findAlbumsFromCategoryId(
        ?int $categoryId,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): AlbumCollection;

    public function findForSearch(array $keys): AlbumCollection;

    public function getAlbumById(int $albumId): ?Album;

    public function getAlbumBySlug(string $slug): ?Album;

    public function countAlbums(
        ?int $categoryId = null,
        ?bool $enabled = null
    ): int;
}
