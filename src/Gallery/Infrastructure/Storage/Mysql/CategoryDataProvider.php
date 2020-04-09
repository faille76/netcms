<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Category;
use App\Gallery\Domain\CategoryCollection;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class CategoryDataProvider extends AbstractDataProvider implements CategoryDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function findCategoriesByParentId(
        ?int $parentId,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): CategoryCollection {
        $categoryCollection = new CategoryCollection();

        $qb = $this->createCategoryQueryBuilder();

        if ($parentId !== null) {
            if ($parentId === 0) {
                $qb->andWhere('category.parent_id IS NULL');
            } else {
                $qb
                    ->andWhere('category.parent_id = :parent_id')
                    ->setParameter('parent_id', $parentId, 'integer');
            }
        }

        if ($enabled !== null) {
            $qb
                ->andWhere('category.enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0, 'integer')
            ;
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $categoryCollection->add($this->createCategoryFromRow($row));
        }

        return $categoryCollection;
    }

    public function getCategoryById(int $categoryId): ?Category
    {
        $qb = $this->createCategoryQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('category.id = :category_id')
            ->setParameter('category_id', $categoryId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createCategoryFromRow($row);
    }

    public function getCategoryBySlug(string $slug): ?Category
    {
        $qb = $this->createCategoryQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('category.slug = :slug')
            ->setParameter('slug', $slug, 'string')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createCategoryFromRow($row);
    }

    private function createCategoryFromRow(array $row): Category
    {
        $row['id'] = (int) $row['id'];
        $row['enabled'] = (bool) $row['enabled'];
        $row['parent_id'] = (int) $row['parent_id'];

        return Category::fromArray($row);
    }

    private function createCategoryQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'category.id',
                'category.name',
                'category.parent_id',
                'category.enabled',
                'category.slug',
                'category.relative_path',
            ])
            ->from('photos_cat', 'category')
        ;
    }
}
