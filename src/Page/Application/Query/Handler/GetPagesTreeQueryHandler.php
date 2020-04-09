<?php

declare(strict_types=1);

namespace App\Page\Application\Query\Handler;

use App\Page\Application\Query\GetPagesTreeQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;

final class GetPagesTreeQueryHandler implements QueryHandlerInterface
{
    private PageDataProviderInterface $pageDataProvider;

    public function __construct(PageDataProviderInterface $pageDataProvider)
    {
        $this->pageDataProvider = $pageDataProvider;
    }

    public function __invoke(GetPagesTreeQuery $query): array
    {
        $pagesList = $this->pageDataProvider->findPagesByParentId(null, $query->getLang())->jsonSerialize();

        return $this->getPagesByParentId($pagesList);
    }

    private function getPagesByParentId(array $pagesList, int $parentId = 0, int $level = 0): array
    {
        $prefix = $this->createPrefix($level);
        $list = [];
        foreach ($pagesList as $page) {
            if ($page['parent_id'] === $parentId) {
                $list[] = [
                    'id' => $page['id'],
                    'name' => $prefix . $page['title'],
                ];
                $list = array_merge($list, $this->getPagesByParentId($pagesList, $page['id'], $level + 1));
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
