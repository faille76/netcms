<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query;

use App\Gallery\Domain\Category;

final class GetSubCategoriesOfCategoryQuery
{
    private ?Category $category;

    public function __construct(?Category $category)
    {
        $this->category = $category;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryId(): int
    {
        if ($this->category !== null) {
            return $this->category->getId();
        }

        return 0;
    }
}
