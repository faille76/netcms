<?php

declare(strict_types=1);

namespace App\Partner\Application\Command;

final class DeletePartnerCommand
{
    private int $partnerId;
    private int $userId;
    private int $occurredOn;

    public function __construct(int $partnerId, int $userId, int $occurredOn)
    {
        $this->partnerId = $partnerId;
        $this->userId = $userId;
        $this->occurredOn = $occurredOn;
    }

    public function getPartnerId(): int
    {
        return $this->partnerId;
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
