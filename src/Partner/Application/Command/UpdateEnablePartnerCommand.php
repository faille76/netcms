<?php

declare(strict_types=1);

namespace App\Partner\Application\Command;

final class UpdateEnablePartnerCommand
{
    private int $partnerId;
    private bool $enabled;
    private int $userId;
    private int $occurredOn;

    public function __construct(int $partnerId, bool $enabled, int $userId, int $occurredOn)
    {
        $this->partnerId = $partnerId;
        $this->enabled = $enabled;
        $this->userId = $userId;
        $this->occurredOn = $occurredOn;
    }

    public function getPartnerId(): int
    {
        return $this->partnerId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
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
