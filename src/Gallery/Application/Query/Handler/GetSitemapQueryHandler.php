<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GetSitemapQuery;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

final class GetSitemapQueryHandler implements QueryHandlerInterface
{
    private CategoryDataProviderInterface $categoryDataProvider;
    private RouterInterface $router;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider,
        RouterInterface $router
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->router = $router;
    }

    public function __invoke(GetSitemapQuery $query): array
    {
        return $this->getCategoryInRec($query->getParentId()) ?? [];
    }

    private function getCategoryInRec(int $categoryId): ?array
    {
        if ($categoryId === 0) {
            return null;
        }
        $category = $this->categoryDataProvider->getCategoryById($categoryId);
        if ($category === null) {
            return null;
        }

        $categories = [];

        $parentCategory = $this->getCategoryInRec($category->getParentId());
        if ($parentCategory) {
            $categories = $parentCategory;
        }

        $categories[] = [
            'slug' => $category->getSlug(),
            'name' => $category->getName(),
        ];

        return $categories;
    }
}
