<?php

declare(strict_types=1);

namespace App\Contact\Domain;

use App\Shared\Domain\ValueObject;
use Assert\Assertion;

final class Contact implements ValueObject
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $role;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        string $role
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    public static function fromArray(array $data)
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'first_name');
        Assertion::keyExists($data, 'last_name');
        Assertion::keyExists($data, 'email');
        Assertion::keyExists($data, 'role');

        return new static(
            $data['id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['role']
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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
