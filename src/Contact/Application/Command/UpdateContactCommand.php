<?php

declare(strict_types=1);

namespace App\Contact\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateContactCommand extends AbstractCommand
{
    private int $contactId;
    private string $lastName;
    private string $firstName;
    private string $email;
    private string $role;

    public function __construct(
        int $contactId,
        string $lastName,
        string $firstName,
        string $email,
        string $role,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->role = $role;
        $this->contactId = $contactId;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
