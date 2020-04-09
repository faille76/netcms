<?php

declare(strict_types=1);

namespace App\PhysicalStorage\Domain\Persister;

interface PhysicalDataPersisterInterface
{
    public function add(string $fromFilePath, string $toFilePath): bool;

    public function remove(string $filePath): bool;

    public function removeDir(string $dirPath): bool;
}
