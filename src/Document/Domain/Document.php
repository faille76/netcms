<?php

declare(strict_types=1);

namespace App\Document\Domain;

use App\Shared\Domain\Author;
use App\Shared\Domain\DateTimeFormat;
use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Document implements ValueObject
{
    private int $id;
    private string $name;
    private string $fileName;
    private Author $author;
    private string $type;
    private bool $enabled;
    private \DateTimeInterface $createdAt;

    public function __construct(
        int $id,
        string $name,
        string $fileName,
        Author $author,
        string $type,
        bool $enabled,
        \DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->fileName = $fileName;
        $this->author = $author;
        $this->type = $type;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_name' => $this->fileName,
            'author' => $this->author->toArray(),
            'type' => $this->type,
            'enabled' => $this->enabled,
            'created_at' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): Document
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'file_name');
        Assertion::keyExists($data, 'author');
        Assertion::keyExists($data, 'type');
        Assertion::keyExists($data, 'enabled');
        Assertion::keyExists($data, 'created_at');

        if (is_string($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DateTimeFormat::FORMAT, $data['created_at']);
        }

        if (is_array($data['author'])) {
            $data['author'] = Author::fromArray($data['author']);
        }

        return new static(
            $data['id'],
            $data['name'],
            $data['file_name'],
            $data['author'],
            $data['type'],
            $data['enabled'],
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

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
