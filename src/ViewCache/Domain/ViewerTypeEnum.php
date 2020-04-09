<?php

declare(strict_types=1);

namespace App\ViewCache\Domain;

final class ViewerTypeEnum
{
    public const ARTICLE = 1;
    public const ALBUM = 2;
    public const PROFILE = 3;

    public static function getTypes(): array
    {
        return [
            self::ARTICLE,
            self::ALBUM,
            self::PROFILE,
        ];
    }
}
