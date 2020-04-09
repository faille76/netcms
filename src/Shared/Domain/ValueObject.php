<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface ValueObject extends \JsonSerializable
{
    public function toArray(): array;

    /**
     * @return mixed
     */
    public static function fromArray(array $data);
}
