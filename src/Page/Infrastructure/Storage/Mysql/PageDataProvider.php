<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Storage\Mysql;

use App\Page\Domain\Page;
use App\Page\Domain\PageCollection;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class PageDataProvider extends AbstractDataProvider implements PageDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getPageBySlug(string $slug, string $lang, ?bool $enabled = null): ?Page
    {
        $qb = $this->createPageQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('page.slug = :slug')
            ->andWhere('page_lang.lang = :lang')
            ->setParameter('slug', $slug, 'string')
            ->setParameter('lang', $lang, 'string')
        ;
        if ($enabled !== null) {
            $qb->andWhere('page.enabled = :enabled');
            $qb->setParameter('enabled', $enabled === true ? 1 : 0, 'integer');
        }

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createPageFromRow($row);
    }

    public function getPageById(int $pageId, string $lang, ?bool $enabled = null): ?Page
    {
        $qb = $this->createPageQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('page.id = :page_id')
            ->andWhere('page_lang.lang = :lang')
            ->setParameter('page_id', $pageId, 'integer')
            ->setParameter('lang', $lang, 'string')
        ;
        if ($enabled !== null) {
            $qb->andWhere('page.enabled = :enabled');
            $qb->setParameter('enabled', $enabled === true ? 1 : 0, 'integer');
        }

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createPageFromRow($row);
    }

    public function findPagesByParentId(
        ?int $parentId,
        string $lang,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): PageCollection {
        $pageCollection = new PageCollection();

        $qb = $this->createPageQueryBuilder(false)
            ->andWhere('page_lang.lang = :lang')
            ->setParameter('lang', $lang, 'string')
        ;

        if ($parentId !== null) {
            if ($parentId === 0) {
                $qb->andWhere('page.parent_id IS NULL');
            } else {
                $qb
                    ->andWhere('page.parent_id = :parent_id')
                    ->setParameter('parent_id', $parentId, 'integer')
                ;
            }
        }

        if ($enabled !== null) {
            $qb->andWhere('page.enabled = :enabled');
            $qb->setParameter('enabled', $enabled === true ? 1 : 0, 'integer');
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $pageCollection->add($this->createPageFromRow($row));
        }

        return $pageCollection;
    }

    public function findForSearch(
        array $keys,
        string $lang
    ): PageCollection {
        $pageCollection = new PageCollection();

        $qb = $this->createPageQueryBuilder(false)
            ->andWhere('page.enabled = :enabled')
            ->andWhere('page_lang.lang = :lang')
            ->setParameter('enabled', 1, 'integer')
            ->setParameter('lang', $lang, 'string')
        ;

        foreach ($keys as $id => $key) {
            $qb
                ->andWhere('page_lang.name LIKE :key' . $id . ' OR page_lang.content LIKE :key' . $id)
                ->setParameter('key' . $id, '%' . addcslashes($key, '%_') . '%')
            ;
        }

        foreach ($this->fetchAll($qb) as $row) {
            $pageCollection->add($this->createPageFromRow($row));
        }

        return $pageCollection;
    }

    private function createPageFromRow(array $row): Page
    {
        $row['id'] = (int) $row['id'];
        $row['parent_id'] = (int) $row['parent_id'];
        $row['enabled'] = (bool) $row['enabled'];
        if (!array_key_exists('content', $row)) {
            $row['content'] = null;
        }

        return Page::fromArray($row);
    }

    private function createPageQueryBuilder(bool $getContent = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder()
            ->select([
                'page.id',
                'page.slug',
                'page.parent_id',
                'page.enabled',
                'page_lang.name as title',
            ])
            ->from('pages', 'page')
            ->innerJoin('page', 'pages_lang', 'page_lang', 'page.id = page_lang.page_id')
        ;
        if ($getContent === true) {
            $qb->addSelect('page_lang.content');
        }

        return $qb;
    }
}
