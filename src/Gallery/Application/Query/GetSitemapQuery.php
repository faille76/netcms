<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query;

final class GetSitemapQuery
{
    private int $parentId;

    public function __construct(int $parentId)
    {
        $this->parentId = $parentId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }
}
