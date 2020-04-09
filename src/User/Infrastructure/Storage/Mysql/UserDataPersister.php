<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Storage\Mysql;

use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;
use App\User\Domain\Persister\UserDataPersisterInterface;

final class UserDataPersister extends AbstractDataPersister implements UserDataPersisterInterface
{
    public function createUser(
        string $lastName,
        string $firstName,
        string $username,
        string $password,
        string $email,
        string $gender,
        string $birthday,
        bool $newsletterEnabled
    ): int {
        $this->connection->insert('users', [
            'username' => $username,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'password' => $password,
            'email' => $email,
            'birthday' => $birthday,
            'gender' => $gender,
            'newsletter' => $newsletterEnabled ? 1 : 0,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function updateSessionId(int $userId, string $sessionId): void
    {
        $this->connection->update('users', [
            'session' => $sessionId,
        ], [
            'id' => $userId,
        ]);
    }

    public function updateUser(int $userId, string $email, ?string $avatar, bool $newsletter): void
    {
        $this->connection->update('users', [
            'email' => $email,
            'avatar' => $avatar,
            'newsletter' => $newsletter ? 1 : 0,
        ], [
            'id' => $userId,
        ]);
    }

    public function updateUserPassword(int $userId, string $password): void
    {
        $this->connection->update('users', [
            'password' => $password,
        ], [
            'id' => $userId,
        ]);
    }
}
