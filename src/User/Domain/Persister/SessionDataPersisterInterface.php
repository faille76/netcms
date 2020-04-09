<?php

declare(strict_types=1);

namespace App\User\Domain\Persister;

interface SessionDataPersisterInterface
{
    public function createSession(
        int $userId,
        string $token,
        string $ip,
        string $userAgent
    ): bool;
}
