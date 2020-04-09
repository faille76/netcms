<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\ImageFactory\Domain\FilterTypeEnum;

final class AvatarImage
{
    public const FILTERS = [FilterTypeEnum::FILTER_600, FilterTypeEnum::FILTER_300];
    public const PATH_BASE = 'avatars/';
    public const FORMAT = 'png';
}
