<?php

declare(strict_types=1);

namespace App\Partner\Application\Event;

final class PartnerCreatedEvent extends AbstractPartnerEvent
{
    public function __construct(int $partnerId, string $name, int $userId, int $occurredOn)
    {
        parent::__construct($partnerId, $userId, $occurredOn);
        $this->addProperty('name', $name);
    }

    public function getName(): string
    {
        return (string) $this->getProperty('name', 'null');
    }
}
