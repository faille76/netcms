<?php

declare(strict_types=1);

namespace App\Page\Domain\Persister;

interface PageDataPersisterInterface
{
    public function createPage(string $slug, int $parentId, bool $enabled): int;

    public function updatePageParentId(int $pageId, int $parentId): void;

    public function updatePageEnabled(int $pageId, bool $enabled): void;

    public function deletePage(int $pageId): void;
}
