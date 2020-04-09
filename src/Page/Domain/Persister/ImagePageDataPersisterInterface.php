<?php

declare(strict_types=1);

namespace App\Page\Domain\Persister;

interface ImagePageDataPersisterInterface
{
    public function deleteByPageId(int $pageId): void;

    public function deleteById(int $id): void;

    public function createImgPage(int $pageId, string $name, string $image, string $size): int;

    public function updateImgPage(int $id, string $name): void;
}
