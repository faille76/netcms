<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Assert\Assertion;

final class Image implements ValueObject
{
    private int $id;
    private ?string $name;
    private string $size;
    private string $url;

    public function __construct(
        int $id,
        ?string $name,
        string $size,
        string $url
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->size = $size;
        $this->url = $url;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'size' => $this->size,
            'url' => $this->url,
        ];
    }

    public static function fromArray(array $data): Image
    {
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'size');
        Assertion::keyExists($data, 'url');

        return new self($data['id'], $data['name'], $data['size'], $data['url']);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
