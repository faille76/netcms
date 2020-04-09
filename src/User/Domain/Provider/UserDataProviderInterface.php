<?php

declare(strict_types=1);

namespace App\User\Domain\Provider;

use App\Shared\Domain\Criteria;
use App\User\Domain\User;
use App\User\Domain\UserCollection;

interface UserDataProviderInterface
{
    public function getUserById(int $userId): ?User;

    public function getUserBySessionId(string $sessionId): ?User;

    public function getUserByEmailOrUsername(string $value): ?User;

    public function findUsers(?Criteria $criteria = null): UserCollection;

    public function countUsers(): int;
}
