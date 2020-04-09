<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query;

final class GenerateRelativePathQuery
{
    private int $categoryId;
    private string $slug;

    public function __construct(int $categoryId, string $slug)
    {
        $this->categoryId = $categoryId;
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
