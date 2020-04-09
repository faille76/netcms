<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class AlbumDataPersister extends AbstractDataPersister implements AlbumDataPersisterInterface
{
    public function createAlbum(
        string $name,
        string $relativePath,
        string $slug,
        int $categoryId,
        bool $enabled,
        int $authorId
    ): int {
        $this->insert('photos_albums', [
            'name' => $name,
            'relative_path' => $relativePath,
            'slug' => $slug,
            'category_id' => $categoryId === 0 ? null : $categoryId,
            'enabled' => $enabled ? 1 : 0,
            'user_id' => $authorId,
        ]);

        return $this->getLastInsertId();
    }

    public function deleteAlbum(int $albumId): void
    {
        $this->delete('photos_albums', [
            'id' => $albumId,
        ]);
    }

    public function updateAlbum(int $albumId, string $name, int $categoryId): void
    {
        $this->update('photos_albums', [
            'name' => $name,
            'category_id' => $categoryId === 0 ? null : $categoryId,
        ], [
            'id' => $albumId,
        ]);
    }

    public function updateAlbumEnabled(int $albumId, bool $enabled): void
    {
        $this->update('photos_albums', [
            'enabled' => $enabled ? 1 : 0,
        ], [
            'id' => $albumId,
        ]);
    }

    public function updateDefaultImageAlbum(int $albumId, ?int $pictureId): void
    {
        $this->update('photos_albums', [
            'ref_picture' => $pictureId,
        ], [
            'id' => $albumId,
        ]);
    }

    public function increaseAlbumView(int $albumId): void
    {
        $this->executeUpdate('UPDATE photos_albums SET view = view + 1 WHERE id = :id', [
            'id' => $albumId,
        ]);
    }

    public function increaseCommentNumber(int $albumId): void
    {
        $this->executeUpdate('UPDATE photos_albums SET nb_comments = nb_comments + 1 WHERE id = :id', [
            'id' => $albumId,
        ]);
    }

    public function decreaseCommentNumber(int $albumId): void
    {
        $this->executeUpdate('UPDATE photos_albums SET nb_comments = nb_comments - 1 WHERE id = :id', [
            'id' => $albumId,
        ]);
    }

    public function increasePictureNumber(int $albumId): void
    {
        $this->executeUpdate('UPDATE photos_albums SET nb_pictures = nb_pictures + 1 WHERE id = :id', [
            'id' => $albumId,
        ]);
    }

    public function decreasePictureNumber(int $albumId): void
    {
        $this->executeUpdate('UPDATE photos_albums SET nb_pictures = nb_pictures - 1 WHERE id = :id', [
            'id' => $albumId,
        ]);
    }
}
