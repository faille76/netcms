<?php

declare(strict_types=1);

namespace App\PhysicalStorage\Domain\Provider;

interface PhysicalDataProviderInterface
{
    public function get(string $filePath): string;

    public function has(string $filePath): bool;
}
