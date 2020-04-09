<?php

declare(strict_types=1);

namespace App\Gallery\Domain;

use App\ImageFactory\Domain\FilterTypeEnum;

final class AlbumPicture
{
    public const FILTERS = [FilterTypeEnum::FILTER_1920, FilterTypeEnum::FILTER_900, FilterTypeEnum::FILTER_250];
    public const PATH_BASE = 'photos/';
    public const FORMAT = 'jpg';
}
