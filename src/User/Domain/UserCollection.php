<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\Collection;

final class UserCollection extends Collection implements \JsonSerializable
{
    public function add(User $user): self
    {
        $this->array[] = $user;

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
