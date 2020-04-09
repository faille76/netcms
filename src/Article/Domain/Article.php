<?php

declare(strict_types=1);

namespace App\Article\Domain;

use App\Shared\Domain\Author;
use App\Shared\Domain\DateTimeFormat;
use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Article implements ValueObject
{
    private int $id;
    private string $slug;
    private string $name;
    private string $content;
    private ?Author $author;
    private ?string $image;
    private int $view;
    private int $commentCount;
    private \DateTimeInterface $createdAt;

    public function __construct(
        int $id,
        string $slug,
        string $name,
        string $content,
        ?Author $author,
        ?string $image,
        int $view,
        int $commentCount,
        \DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->slug = $slug;
        $this->name = $name;
        $this->content = $content;
        $this->author = $author;
        $this->image = $image;
        $this->view = $view;
        $this->createdAt = $createdAt;
        $this->commentCount = $commentCount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'content' => $this->content,
            'author' => $this->author !== null ? $this->author->toArray() : null,
            'image' => $this->image,
            'view' => $this->view,
            'comment_count' => $this->commentCount,
            'created_at' => $this->createdAt->format(DateTimeFormat::FORMAT),
        ];
    }

    public static function fromArray(array $data): Article
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'slug');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'content');
        Assertion::keyExists($data, 'author');
        Assertion::keyExists($data, 'image');
        Assertion::keyExists($data, 'view');
        Assertion::keyExists($data, 'comment_count');
        Assertion::keyExists($data, 'created_at');

        if (is_string($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['created_at']);
        }

        if (is_array($data['author'])) {
            $data['author'] = Author::fromArray($data['author']);
        }

        return new static(
            $data['id'],
            $data['slug'],
            $data['name'],
            $data['content'],
            $data['author'],
            $data['image'],
            $data['view'],
            $data['comment_count'],
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getView(): int
    {
        return $this->view;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
