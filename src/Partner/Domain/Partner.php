<?php

declare(strict_types=1);

namespace App\Partner\Domain;

use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Partner implements ValueObject
{
    private int $id;
    private string $name;
    private string $image;
    private string $url;
    private bool $enabled;

    public function __construct(
        int $id,
        string $name,
        string $image,
        string $url,
        bool $enabled
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->url = $url;
        $this->enabled = $enabled;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'url' => $this->url,
            'enabled' => $this->enabled,
        ];
    }

    public static function fromArray(array $data): Partner
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'image');
        Assertion::keyExists($data, 'url');
        Assertion::keyExists($data, 'enabled');

        return new static(
            $data['id'],
            $data['name'],
            $data['image'],
            $data['url'],
            $data['enabled']
        );
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
