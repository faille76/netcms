<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreateSessionCommand extends AbstractCommand
{
    private string $ip;
    private string $userAgent;

    public function __construct(
        string $ip,
        string $userAgent,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
