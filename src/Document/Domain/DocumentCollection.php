<?php

declare(strict_types=1);

namespace App\Document\Domain;

use App\Shared\Domain\Collection;

final class DocumentCollection extends Collection implements \JsonSerializable
{
    public function add(Document $document): self
    {
        $this->array[] = $document;

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
