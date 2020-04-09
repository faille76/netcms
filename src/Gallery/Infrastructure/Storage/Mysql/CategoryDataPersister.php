<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Persister\CategoryDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class CategoryDataPersister extends AbstractDataPersister implements CategoryDataPersisterInterface
{
    public function createCategory(string $name, string $relativePath, string $slug, int $parentId, bool $enabled): int
    {
        $this->insert('photos_cat', [
            'name' => $name,
            'relative_path' => $relativePath,
            'slug' => $slug,
            'parent_id' => $parentId === 0 ? null : $parentId,
            'enabled' => $enabled ? 1 : 0,
        ]);

        return $this->getLastInsertId();
    }

    public function deleteCategory(int $categoryId): void
    {
        $this->delete('photos_cat', [
            'id' => $categoryId,
        ]);
    }

    public function updateCategory(int $categoryId, string $name, int $parentId): void
    {
        $this->update('photos_cat', [
            'name' => $name,
            'parent_id' => $parentId === 0 ? null : $parentId,
        ], [
            'id' => $categoryId,
        ]);
    }

    public function updateCategoryEnabled(int $categoryId, bool $enabled): void
    {
        $this->update('photos_cat', [
            'enabled' => $enabled ? 1 : 0,
        ], [
            'id' => $categoryId,
        ]);
    }
}
