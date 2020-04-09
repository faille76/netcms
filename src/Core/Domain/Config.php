<?php

declare(strict_types=1);

namespace App\Core\Domain;

use App\Shared\Domain\ValueObject;

final class Config implements ValueObject
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param mixed|null $value
     */
    public function add(string $key, $value = null): self
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        return $default;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public static function fromArray(array $data): Config
    {
        return new static($data);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
