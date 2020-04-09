<?php

declare(strict_types=1);

namespace App\Page\Domain;

use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Page implements ValueObject
{
    private int $id;
    private string $slug;
    private int $parentId;
    private string $title;
    private ?string $content;
    private bool $enabled;

    public function __construct(
        int $id,
        string $slug,
        int $parentId,
        string $title,
        ?string $content,
        bool $enabled
    ) {
        $this->id = $id;
        $this->slug = $slug;
        $this->parentId = $parentId;
        $this->title = $title;
        $this->content = $content;
        $this->enabled = $enabled;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'parent_id' => $this->parentId,
            'title' => $this->title,
            'content' => $this->content,
            'enabled' => $this->enabled,
        ];
    }

    public static function fromArray(array $data): Page
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'slug');
        Assertion::keyExists($data, 'parent_id');
        Assertion::keyExists($data, 'title');
        Assertion::keyExists($data, 'content');
        Assertion::keyExists($data, 'enabled');

        return new static(
            $data['id'],
            $data['slug'],
            $data['parent_id'],
            $data['title'],
            $data['content'],
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
