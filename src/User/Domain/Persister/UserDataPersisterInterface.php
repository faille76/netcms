<?php

declare(strict_types=1);

namespace App\User\Domain\Persister;

interface UserDataPersisterInterface
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
    ): int;

    public function updateSessionId(int $userId, string $sessionId): void;

    public function updateUser(int $userId, string $email, ?string $avatar, bool $newsletter): void;

    public function updateUserPassword(int $userId, string $password): void;
}
