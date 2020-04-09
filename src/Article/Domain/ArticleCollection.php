<?php

declare(strict_types=1);

namespace App\Article\Domain;

use App\Shared\Domain\Collection;

final class ArticleCollection extends Collection implements \JsonSerializable
{
    public function add(Article $article): self
    {
        $this->array[] = $article;

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
