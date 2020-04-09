<?php

declare(strict_types=1);

namespace App\Gallery\Domain;

use App\Shared\Domain\Author;
use App\Shared\Domain\DateTimeFormat;
use App\Shared\Domain\Image;
use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Album implements ValueObject
{
    private int $id;
    private string $name;
    private string $slug;
    private string $relativePath;
    private int $parentId;
    private bool $enabled;
    private ?Author $author;
    private int $view;
    private int $pictureCount;
    private int $commentCount;
    private ?Image $pictureCover;
    private \DateTimeInterface $createdAt;

    public function __construct(
        int $id,
        string $name,
        string $slug,
        string $relativePath,
        int $parentId,
        bool $enabled,
        ?Author $author,
        int $view,
        int $pictureCount,
        int $commentCount,
        ?Image $pictureCover,
        \DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->relativePath = $relativePath;
        $this->parentId = $parentId;
        $this->enabled = $enabled;
        $this->author = $author;
        $this->view = $view;
        $this->createdAt = $createdAt;
        $this->pictureCount = $pictureCount;
        $this->pictureCover = $pictureCover;
        $this->commentCount = $commentCount;
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
            'author' => $this->author !== null ? $this->author->toArray() : null,
            'view' => $this->view,
            'picture_count' => $this->pictureCount,
            'comment_count' => $this->commentCount,
            'picture_cover' => $this->pictureCover ? $this->pictureCover->toArray() : null,
            'created_at' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): Album
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'slug');
        Assertion::keyExists($data, 'relative_path');
        Assertion::keyExists($data, 'parent_id');
        Assertion::keyExists($data, 'enabled');
        Assertion::keyExists($data, 'author');
        Assertion::keyExists($data, 'view');
        Assertion::keyExists($data, 'picture_count');
        Assertion::keyExists($data, 'comment_count');
        Assertion::keyExists($data, 'picture_cover');
        Assertion::keyExists($data, 'created_at');

        if (is_string($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['created_at']);
        }

        if (is_array($data['author'])) {
            $data['author'] = Author::fromArray($data['author']);
        }

        if (is_array($data['picture_cover'])) {
            $data['picture_cover'] = Image::fromArray($data['picture_cover']);
        }

        return new static(
            $data['id'],
            $data['name'],
            $data['slug'],
            $data['relative_path'],
            $data['parent_id'],
            $data['enabled'],
            $data['author'],
            $data['view'],
            $data['picture_count'],
            $data['comment_count'],
            $data['picture_cover'],
            $data['created_at']
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

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function getView(): int
    {
        return $this->view;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getPictureCount(): int
    {
        return $this->pictureCount;
    }

    public function getPictureCover(): ?Image
    {
        return $this->pictureCover;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }
}
