<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GetSubCategoriesOfCategoryQuery;
use App\Gallery\Domain\Category;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use App\Shared\Domain\Criteria;

final class GetSubCategoriesOfCategoryQueryHandler implements QueryHandlerInterface
{
    private CategoryDataProviderInterface $categoryDataProvider;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    public function __invoke(GetSubCategoriesOfCategoryQuery $query): array
    {
        $parentId = $query->getCategoryId();
        $categoryCollection = $this->categoryDataProvider->findCategoriesByParentId(
            $parentId,
            true,
            new Criteria(['id' => 'DESC'])
        );

        $categoryList = [];
        /** @var Category $category */
        foreach ($categoryCollection as $category) {
            $item = $category->toArray();
            $item['sub'] = $this->categoryDataProvider->findCategoriesByParentId(
                $category->getId(),
                true,
                new Criteria(['id' => 'DESC'])
            );
            $categoryList[] = $item;
        }

        return $categoryList;
    }
}
