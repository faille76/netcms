<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class ImageCollection extends Collection implements \JsonSerializable
{
    public function add(Image $image): self
    {
        $this->array[] = $image;

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
