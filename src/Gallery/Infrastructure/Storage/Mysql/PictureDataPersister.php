<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Persister\PictureDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class PictureDataPersister extends AbstractDataPersister implements PictureDataPersisterInterface
{
    public function createPicture(int $albumId, string $fileName, string $size): int
    {
        $this->insert('photos_img', [
            'album_id' => $albumId,
            'name' => $fileName,
            'size' => $size,
        ]);

        return $this->getLastInsertId();
    }

    public function deletePicture(int $pictureId): void
    {
        $this->delete('photos_img', [
            'id' => $pictureId,
        ]);
    }

    public function deletePicturesByAlbumId(int $albumId): void
    {
        $this->delete('photos_img', [
            'album_id' => $albumId,
        ]);
    }
}
