<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Storage\Mysql;

use App\Page\Domain\Persister\PageDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class PageDataPersister extends AbstractDataPersister implements PageDataPersisterInterface
{
    public function createPage(string $slug, int $parentId, bool $enabled): int
    {
        $this->insert('pages', [
            'slug' => $slug,
            'parent_id' => $parentId === 0 ? null : $parentId,
            'enabled' => $enabled ? 1 : 0,
        ]);

        return $this->getLastInsertId();
    }

    public function updatePageParentId(int $pageId, int $parentId): void
    {
        $this->update('pages', [
            'parent_id' => $parentId === 0 ? null : $parentId,
        ], [
            'id' => $pageId,
        ]);
    }

    public function updatePageEnabled(int $pageId, bool $enabled): void
    {
        $this->update('pages', [
            'enabled' => $enabled ? 1 : 0,
        ], [
            'id' => $pageId,
        ]);
    }

    public function deletePage(int $pageId): void
    {
        $this->delete('pages', [
            'id' => $pageId,
        ]);
    }
}
