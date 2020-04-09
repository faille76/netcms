<?php

declare(strict_types=1);

namespace App\ImageFactory\Domain;

final class FilterTypeEnum
{
    public const FILTER_150 = 0;
    public const FILTER_200 = 1;
    public const FILTER_250 = 2;
    public const FILTER_300 = 3;
    public const FILTER_600 = 4;
    public const FILTER_900 = 5;
    public const FILTER_1200 = 6;
    public const FILTER_1920 = 7;

    public const FILTER_DEFINITION = [
        self::FILTER_150 => [
            'name' => '150',
            'width' => 150,
            'height' => 93,
        ],
        self::FILTER_200 => [
            'name' => '200',
            'width' => 200,
            'height' => 125,
        ],
        self::FILTER_250 => [
            'name' => '250',
            'width' => 250,
            'height' => 156,
        ],
        self::FILTER_300 => [
            'name' => '300',
            'width' => 300,
            'height' => 187,
        ],
        self::FILTER_600 => [
            'name' => '600',
            'width' => 600,
            'height' => 375,
        ],
        self::FILTER_900 => [
            'name' => '900',
            'width' => 900,
            'height' => 562,
        ],
        self::FILTER_1200 => [
            'name' => '1200',
            'width' => 1200,
            'height' => 750,
        ],
        self::FILTER_1920 => [
            'name' => '1920',
            'width' => 1920,
            'height' => 1080,
        ],
    ];

    public static function getFilterName(int $filterType): string
    {
        if (!isset(self::FILTER_DEFINITION[$filterType])) {
            throw new \InvalidArgumentException('Filter type does not exists.');
        }

        return self::FILTER_DEFINITION[$filterType]['name'];
    }

    public static function getFilterWidth(int $filterType): int
    {
        if (!isset(self::FILTER_DEFINITION[$filterType])) {
            throw new \InvalidArgumentException('Filter type does not exists.');
        }

        return self::FILTER_DEFINITION[$filterType]['width'];
    }

    public static function getFilterHeight(int $filterType): int
    {
        if (!isset(self::FILTER_DEFINITION[$filterType])) {
            throw new \InvalidArgumentException('Filter type does not exists.');
        }

        return self::FILTER_DEFINITION[$filterType]['height'];
    }
}
