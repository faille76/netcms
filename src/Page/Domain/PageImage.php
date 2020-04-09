<?php

declare(strict_types=1);

namespace App\Page\Domain;

use App\ImageFactory\Domain\FilterTypeEnum;

final class PageImage
{
    public const FILTERS = [FilterTypeEnum::FILTER_250, FilterTypeEnum::FILTER_900, FilterTypeEnum::FILTER_1920];
    public const PATH_BASE = 'pages/';
    public const FORMAT = 'png';
}
