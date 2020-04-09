<?php

declare(strict_types=1);

namespace App\Document\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractDocumentEvent extends AbstractUserEvent
{
    public function __construct(int $documentId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('documentId', $documentId);
    }

    public function getDocumentId(): int
    {
        return (int) $this->getProperty('documentId', 0);
    }
}
