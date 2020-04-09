<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Persister;

interface AlbumDataPersisterInterface
{
    public function createAlbum(
        string $name,
        string $relativePath,
        string $slug,
        int $categoryId,
        bool $enabled,
        int $authorId
    ): int;

    public function deleteAlbum(int $albumId): void;

    public function updateAlbum(int $albumId, string $name, int $categoryId): void;

    public function updateAlbumEnabled(int $albumId, bool $enabled): void;

    public function updateDefaultImageAlbum(int $albumId, ?int $pictureId): void;

    public function increaseAlbumView(int $albumId): void;

    public function increaseCommentNumber(int $albumId): void;
    public function decreaseCommentNumber(int $albumId): void;

    public function increasePictureNumber(int $albumId): void;
    public function decreasePictureNumber(int $albumId): void;
}
