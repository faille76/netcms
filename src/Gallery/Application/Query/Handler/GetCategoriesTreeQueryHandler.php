<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GetCategoriesTreeQuery;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;

final class GetCategoriesTreeQueryHandler implements QueryHandlerInterface
{
    private CategoryDataProviderInterface $categoryDataProvider;

    public function __construct(CategoryDataProviderInterface $categoryDataProvider)
    {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    public function __invoke(GetCategoriesTreeQuery $query): array
    {
        $categoriesList = $this->categoryDataProvider->findCategoriesByParentId(null)->jsonSerialize();

        return $this->getCategoriesByParentId($categoriesList);
    }

    private function getCategoriesByParentId(array $categoriesList, int $parentId = 0, int $level = 0): array
    {
        $prefix = $this->createPrefix($level);
        $list = [];
        foreach ($categoriesList as $category) {
            if ($category['parent_id'] === $parentId) {
                $list[] = [
                    'id' => $category['id'],
                    'name' => $prefix . $category['name'],
                ];
                $list = array_merge($list, $this->getCategoriesByParentId($categoriesList, $category['id'], $level + 1));
            }
        }

        return $list;
    }

    private function createPrefix(int $level): string
    {
        $str = str_repeat('-', $level + 1);

        return $str . '> ';
    }
}
