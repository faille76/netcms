<?php

declare(strict_types=1);

namespace App\Comment\Domain;

use App\Shared\Domain\Author;
use App\Shared\Domain\DateTimeFormat;
use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Comment implements ValueObject
{
    private int $id;
    private int $articleId;
    private string $content;
    private Author $author;
    private int $type;
    private \DateTimeInterface $createdAt;

    public function __construct(
        int $id,
        int $articleId,
        string $content,
        Author $author,
        int $type,
        \DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->articleId = $articleId;
        $this->content = $content;
        $this->author = $author;
        $this->type = $type;
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'article_id' => $this->articleId,
            'content' => $this->content,
            'author' => $this->author->toArray(),
            'type' => $this->type,
            'created_at' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data)
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'article_id');
        Assertion::keyExists($data, 'content');
        Assertion::keyExists($data, 'author');
        Assertion::keyExists($data, 'type');
        Assertion::keyExists($data, 'created_at');

        if (is_string($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['created_at']);
        }

        if (is_array($data['author'])) {
            $data['author'] = Author::fromArray($data['author']);
        }

        return new static(
            $data['id'],
            $data['article_id'],
            $data['content'],
            $data['author'],
            $data['type'],
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

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
