<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Provider;

use App\Shared\Domain\Criteria;
use App\Shared\Domain\Image;
use App\Shared\Domain\ImageCollection;

interface PictureDataProviderInterface
{
    public function getPicture(int $pictureId): ?Image;

    public function findPicturesByAlbumId(int $albumId, ?Criteria $criteria = null): ImageCollection;

    public function findOneRandomPicture(int $albumId): ?Image;

    public function countPictures(?int $albumId = null): int;
}
