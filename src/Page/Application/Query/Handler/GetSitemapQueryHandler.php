<?php

declare(strict_types=1);

namespace App\Page\Application\Query\Handler;

use App\Page\Application\Query\GetSitemapQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;

final class GetSitemapQueryHandler implements QueryHandlerInterface
{
    private PageDataProviderInterface $pageDataProvider;

    public function __construct(PageDataProviderInterface $pageDataProvider)
    {
        $this->pageDataProvider = $pageDataProvider;
    }

    public function __invoke(GetSitemapQuery $query): array
    {
        return $this->getPageInRec($query->getParentId(), $query->getLang()) ?? [];
    }

    private function getPageInRec(int $pageId, string $lang): ?array
    {
        if ($pageId === 0) {
            return null;
        }
        $page = $this->pageDataProvider->getPageById($pageId, $lang);
        if ($page === null) {
            return null;
        }

        $pages = [];

        $parentPage = $this->getPageInRec($page->getParentId(), $lang);
        if ($parentPage) {
            $pages = $parentPage;
        }

        $pages[] = [
            'slug' => $page->getSlug(),
            'name' => $page->getTitle(),
        ];

        return $pages;
    }
}
