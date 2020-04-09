<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Assert\Assertion;

final class Author implements ValueObject
{
    private int $userId;
    private string $firstName;
    private string $lastName;
    private ?string $avatar;

    public function __construct(
        int $userId,
        string $firstName,
        string $lastName,
        ?string $avatar
    ) {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->avatar = $avatar;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'avatar' => $this->avatar,
        ];
    }

    public static function fromArray(array $data): Author
    {
        Assertion::keyExists($data, 'user_id');
        Assertion::keyExists($data, 'first_name');
        Assertion::keyExists($data, 'last_name');
        Assertion::keyExists($data, 'avatar');

        return new static($data['user_id'], $data['first_name'], $data['last_name'], $data['avatar']);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
