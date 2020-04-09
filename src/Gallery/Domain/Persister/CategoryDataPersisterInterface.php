<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Persister;

interface CategoryDataPersisterInterface
{
    public function createCategory(
        string $name,
        string $relativePath,
        string $slug,
        int $parentId,
        bool $enabled
    ): int;

    public function deleteCategory(int $categoryId): void;

    public function updateCategory(int $categoryId, string $name, int $parentId): void;

    public function updateCategoryEnabled(int $categoryId, bool $enabled): void;
}
