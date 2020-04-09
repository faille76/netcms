<?php

declare(strict_types=1);

namespace App\Gallery\Domain\Provider;

use App\Gallery\Domain\Category;
use App\Gallery\Domain\CategoryCollection;
use App\Shared\Domain\Criteria;

interface CategoryDataProviderInterface
{
    public function findCategoriesByParentId(
        ?int $parentId,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): CategoryCollection;

    public function getCategoryById(int $categoryId): ?Category;

    public function getCategoryBySlug(string $slug): ?Category;
}
