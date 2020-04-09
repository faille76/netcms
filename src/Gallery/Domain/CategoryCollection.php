<?php

declare(strict_types=1);

namespace App\Gallery\Domain;

use App\Shared\Domain\Collection;

final class CategoryCollection extends Collection implements \JsonSerializable
{
    public function add(Category $category): self
    {
        $this->array[] = $category;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        $array = [];
        foreach ($this->array as $item) {
            $array[] = $item->jsonSerialize();
        }

        return $array;
    }
}
