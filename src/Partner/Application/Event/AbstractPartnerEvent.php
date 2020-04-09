<?php

declare(strict_types=1);

namespace App\Partner\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractPartnerEvent extends AbstractUserEvent
{
    public function __construct(int $partnerId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('partnerId', $partnerId);
    }

    public function getPartnerId(): int
    {
        return (int) $this->getProperty('partnerId', 0);
    }
}
