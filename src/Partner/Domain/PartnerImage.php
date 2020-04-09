<?php

declare(strict_types=1);

namespace App\Partner\Domain;

use App\ImageFactory\Domain\FilterTypeEnum;

final class PartnerImage
{
    public const FILTERS = [FilterTypeEnum::FILTER_250, FilterTypeEnum::FILTER_150];
    public const PATH_BASE = 'partners/';
    public const FORMAT = 'png';
}
