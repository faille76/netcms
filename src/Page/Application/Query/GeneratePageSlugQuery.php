<?php

declare(strict_types=1);

namespace App\Page\Application\Query;

final class GeneratePageSlugQuery
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
