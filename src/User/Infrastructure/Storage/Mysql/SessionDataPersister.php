<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Storage\Mysql;

use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;
use App\User\Domain\Persister\SessionDataPersisterInterface;

final class SessionDataPersister extends AbstractDataPersister implements SessionDataPersisterInterface
{
    public function createSession(int $userId, string $token, string $ip, string $userAgent): bool
    {
        $res = $this->insert('sessions', [
            'token' => $token,
            'user_id' => $userId,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);

        return $res > 0;
    }
}
