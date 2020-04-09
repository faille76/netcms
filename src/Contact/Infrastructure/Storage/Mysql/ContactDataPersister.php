<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\Storage\Mysql;

use App\Contact\Domain\Persister\ContactDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class ContactDataPersister extends AbstractDataPersister implements ContactDataPersisterInterface
{
    public function createContact(string $lastName, string $firstName, string $email, string $role): int
    {
        $this->insert('contacts', [
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'role' => $role,
        ]);

        return $this->getLastInsertId();
    }

    public function updateContact(int $contactId, string $lastName, string $firstName, string $email, string $role): void
    {
        $this->update('contacts', [
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'role' => $role,
        ], [
            'id' => $contactId,
        ]);
    }

    public function deleteContact(int $contactId): void
    {
        $this->delete('contacts', [
            'id' => $contactId,
        ]);
    }
}
