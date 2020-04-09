<?php

declare(strict_types=1);

namespace App\Article\Application\Query;

final class GenerateArticleSlugQuery
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
