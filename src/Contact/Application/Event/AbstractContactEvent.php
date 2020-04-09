<?php

declare(strict_types=1);

namespace App\Contact\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractContactEvent extends AbstractUserEvent
{
    public function __construct(int $contactId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('contactId', $contactId);
    }

    public function getContactId(): int
    {
        return (int) $this->getProperty('contactId', 0);
    }
}
