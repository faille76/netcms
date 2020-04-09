<?php

declare(strict_types=1);

namespace App\Article\Domain;

use App\ImageFactory\Domain\FilterTypeEnum;

final class ArticleImage
{
    public const FILTERS = [FilterTypeEnum::FILTER_600, FilterTypeEnum::FILTER_300];
    public const PATH_BASE = 'articles/';
    public const FORMAT = 'png';
}
