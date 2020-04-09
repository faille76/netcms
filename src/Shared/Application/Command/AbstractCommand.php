<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

abstract class AbstractCommand
{
    protected int $userId;
    protected int $occurredOn;

    public function __construct(
        int $userId,
        int $occurredOn
    ) {
        $this->userId = $userId;
        $this->occurredOn = $occurredOn;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getOccurredOn(): int
    {
        return $this->occurredOn;
    }
}
