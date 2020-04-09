<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

abstract class AbstractUserEvent extends AbstractBaseEvent
{
    public function __construct(int $userId, int $occurredOn)
    {
        parent::__construct($occurredOn);
        $this->addProperty('userId', $userId);
    }

    public function getUserId(): int
    {
        return (int) $this->getProperty('userId', 0);
    }
}
