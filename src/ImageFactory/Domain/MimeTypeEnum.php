<?php

declare(strict_types=1);

namespace App\ImageFactory\Domain;

final class MimeTypeEnum
{
    public const JPEG = 'image/jpeg';
    public const PNG = 'image/png';
    public const GIF = 'image/gif';

    public static function toArray(): array
    {
        return [
            self::JPEG,
            self::PNG,
            self::GIF,
        ];
    }
}
