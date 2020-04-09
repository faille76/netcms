<?php

declare(strict_types=1);

namespace App\User\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

final class SessionCreatedEvent extends AbstractUserEvent
{
    public function __construct(
        string $ip,
        string $userAgent,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->addProperties([
            'ip' => $ip,
            'userAgent' => $userAgent,
        ]);
    }

    public function getIp(): string
    {
        return (string) $this->getProperty('ip', 'null');
    }

    public function getUserAgent(): string
    {
        return (string) $this->getProperty('userAgent', 'null');
    }
}
