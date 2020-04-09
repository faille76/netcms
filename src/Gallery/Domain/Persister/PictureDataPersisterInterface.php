<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Persister;

interface PictureDataPersisterInterface
{
    public function createPicture(int $albumId, string $fileName, string $size): int;

    public function deletePicture(int $pictureId): void;

    public function deletePicturesByAlbumId(int $albumId): void;
}
