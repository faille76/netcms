<?php

declare(strict_types=1);

namespace App\Page\Application\Query\Handler;

use App\Page\Application\Query\GetNavPagesTreeQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use App\Shared\Domain\Criteria;

final class GetNavPagesTreeQueryHandler implements QueryHandlerInterface
{
    private PageDataProviderInterface $pageDataProvider;

    public function __construct(PageDataProviderInterface $pageDataProvider)
    {
        $this->pageDataProvider = $pageDataProvider;
    }

    public function __invoke(GetNavPagesTreeQuery $query): array
    {
        $pagesList = $this->pageDataProvider->findPagesByParentId(
            null,
            $query->getLang(),
            true,
            new Criteria(['id' => 'ASC'])
        )->jsonSerialize();

        return $this->getPagesByParentId($pagesList);
    }

    private function getPagesByParentId(array $pagesList, int $parentId = 0): array
    {
        $list = [];
        foreach ($pagesList as $page) {
            if ($page['parent_id'] === $parentId) {
                $list[$page['id']] = [
                    'name' => $page['title'],
                    'page_name' => $page['slug'],
                    'sub' => false,
                    'sub_page' => $this->getPagesByParentId($pagesList, $page['id']),
                ];
                if (count($list[$page['id']]['sub_page']) > 0) {
                    $list[$page['id']]['sub'] = true;
                }
            }
        }

        return $list;
    }
}
