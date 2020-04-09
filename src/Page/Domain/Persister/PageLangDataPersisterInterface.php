<?php

declare(strict_types=1);

namespace App\Page\Domain\Persister;

interface PageLangDataPersisterInterface
{
    public function createPageLang(int $pageId, string $lang, string $name, ?string $content): int;

    public function updatePageLang(int $pageId, string $lang, string $name, ?string $content): void;

    public function deletePageLang(int $pageId, string $lang): void;

    public function deleteByPageId(int $pageId): void;
}
