<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GenerateCategorySlugQuery;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use Cocur\Slugify\SlugifyInterface;

final class GenerateCategorySlugQueryHandler implements QueryHandlerInterface
{
    private CategoryDataProviderInterface $categoryDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider,
        SlugifyInterface $slugify
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->slugify = $slugify;
    }

    public function __invoke(GenerateCategorySlugQuery $query): string
    {
        $slug = $this->slugify->slugify($query->getName());

        if ($this->categoryDataProvider->getCategoryBySlug($slug) === null) {
            return $slug;
        }

        $base = $slug;
        $i = 1;
        do {
            $slug = $base . '-' . $i;
            $i++;
        } while ($this->categoryDataProvider->getCategoryBySlug($slug) !== null);

        return $slug;
    }
}
