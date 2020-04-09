<?php

declare(strict_types=1);

namespace App\Page\Application\Query;

final class GetSitemapQuery
{
    private int $parentId;
    private string $lang;

    public function __construct(int $parentId, string $lang)
    {
        $this->parentId = $parentId;
        $this->lang = $lang;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}
