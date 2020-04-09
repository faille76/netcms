<?php

declare(strict_types=1);

namespace App\Partner\Domain;

use App\Shared\Domain\Collection;

final class PartnerCollection extends Collection implements \JsonSerializable
{
    public function add(Partner $partner): self
    {
        $this->array[] = $partner;

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
