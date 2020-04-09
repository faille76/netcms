<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Storage\Mysql;

use App\Page\Domain\Persister\PageLangDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class PageLangDataPersister extends AbstractDataPersister implements PageLangDataPersisterInterface
{
    public function createPageLang(int $pageId, string $lang, string $name, ?string $content): int
    {
        $this->insert('pages_lang', [
            'page_id' => $pageId,
            'lang' => $lang,
            'name' => $name,
            'content' => $content,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function updatePageLang(int $pageId, string $lang, string $name, ?string $content): void
    {
        $this->update('pages_lang', [
            'name' => $name,
            'content' => $content,
        ], [
            'page_id' => $pageId,
            'lang' => $lang,
        ]);
    }

    public function deletePageLang(int $pageId, string $lang): void
    {
        $this->delete('pages_lang', [
            'page_id' => $pageId,
            'lang' => $lang,
        ]);
    }

    public function deleteByPageId(int $pageId): void
    {
        $this->delete('pages_lang', [
            'page_id' => $pageId,
        ]);
    }
}
