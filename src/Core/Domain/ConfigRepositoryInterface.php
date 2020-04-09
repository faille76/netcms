<?php

declare(strict_types=1);

namespace App\Core\Domain;

interface ConfigRepositoryInterface
{
    public function getAll(): array;

    /**
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * @param mixed|null $value
     */
    public function update(string $key, $value): void;
}
