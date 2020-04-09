<?php

declare(strict_types=1);

namespace App\Gallery\Domain;

use App\Shared\Domain\Collection;

final class AlbumCollection extends Collection implements \JsonSerializable
{
    public function add(Album $album): self
    {
        $this->array[] = $album;

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
