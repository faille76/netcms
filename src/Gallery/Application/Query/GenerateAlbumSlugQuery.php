<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query;

final class GenerateAlbumSlugQuery
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
