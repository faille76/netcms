<?php

declare(strict_types=1);

namespace App\Gallery\Domain;

use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Category implements ValueObject
{
    private int $id;
    private string $name;
    private string $slug;
    private string $relativePath;
    private int $parentId;
    private bool $enabled;

    public function __construct(
        int $id,
        string $name,
        string $slug,
        string $relativePath,
        int $parentId,
        bool $enabled
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->relativePath = $relativePath;
        $this->parentId = $parentId;
        $this->enabled = $enabled;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'relative_path' => $this->relativePath,
            'parent_id' => $this->parentId,
            'enabled' => $this->enabled,
        ];
    }

    public static function fromArray(array $data): Category
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'slug');
        Assertion::keyExists($data, 'relative_path');
        Assertion::keyExists($data, 'parent_id');
        Assertion::keyExists($data, 'enabled');

        return new static(
            $data['id'],
            $data['name'],
            $data['slug'],
            $data['relative_path'],
            $data['parent_id'],
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
