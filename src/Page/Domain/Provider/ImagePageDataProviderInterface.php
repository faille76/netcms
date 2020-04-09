<?php

declare(strict_types=1);

namespace App\Page\Domain\Provider;

use App\Shared\Domain\Criteria;
use App\Shared\Domain\Image;
use App\Shared\Domain\ImageCollection;

interface ImagePageDataProviderInterface
{
    public function findImagesByPageId(int $pageId, ?Criteria $criteria = null): ImageCollection;

    public function getImageByIdAndPageId(int $imgId, int $pageId): ?Image;

    public function getImageById(int $imgId): ?Image;
}
