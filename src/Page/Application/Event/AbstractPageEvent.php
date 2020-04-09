<?php

declare(strict_types=1);

namespace App\Page\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractPageEvent extends AbstractUserEvent
{
    public function __construct(
        int $pageId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('pageId', $pageId);
    }

    public function getPageId(): int
    {
        return (int) $this->getProperty('pageId', 0);
    }
}
