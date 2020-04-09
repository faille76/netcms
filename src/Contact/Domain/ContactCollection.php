<?php

declare(strict_types=1);

namespace App\Contact\Domain;

use App\Shared\Domain\Collection;

final class ContactCollection extends Collection implements \JsonSerializable
{
    public function add(Contact $contact): self
    {
        $this->array[] = $contact;

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
