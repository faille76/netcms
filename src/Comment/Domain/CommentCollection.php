<?php

declare(strict_types=1);

namespace App\Comment\Domain;

use App\Shared\Domain\Collection;

final class CommentCollection extends Collection implements \JsonSerializable
{
    public function add(Comment $comment): self
    {
        $this->array[] = $comment;

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
