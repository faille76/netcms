<?php

declare(strict_types=1);

namespace App\Contact\Domain\Persister;

interface ContactDataPersisterInterface
{
    public function createContact(
        string $lastName,
        string $firstName,
        string $email,
        string $role
    ): int;

    public function updateContact(
        int $contactId,
        string $lastName,
        string $firstName,
        string $email,
        string $role
    ): void;

    public function deleteContact(int $contactId): void;
}
