<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Iterator;

class Collection implements Iterator
{
    private int $position = 0;
    protected array $array = [];

    /**
     * @return mixed
     * @inheritdoc
     */
    public function current()
    {
        return $this->array[$this->position];
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->array);
    }
}
