<?php

declare(strict_types=1);

namespace App\Page\Application\Query;

final class GetNavPagesTreeQuery
{
    private string $lang;

    public function __construct(string $lang)
    {
        $this->lang = $lang;
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}
