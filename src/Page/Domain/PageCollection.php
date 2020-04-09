<?php

declare(strict_types=1);

namespace App\Page\Domain;

use App\Shared\Domain\Collection;

final class PageCollection extends Collection implements \JsonSerializable
{
    public function add(Page $page): self
    {
        $this->array[] = $page;

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
