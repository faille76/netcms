<?php

declare(strict_types=1);

namespace App\Page\Domain\Provider;

use App\Page\Domain\Page;
use App\Page\Domain\PageCollection;
use App\Shared\Domain\Criteria;

interface PageDataProviderInterface
{
    public function getPageBySlug(string $slug, string $lang, ?bool $enabled = null): ?Page;

    public function getPageById(int $pageId, string $lang, ?bool $enabled = null): ?Page;

    public function findPagesByParentId(
        ?int $parentId,
        string $lang,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): PageCollection;

    public function findForSearch(
        array $keys,
        string $lang
    ): PageCollection;
}
