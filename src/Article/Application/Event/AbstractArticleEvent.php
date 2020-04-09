<?php

declare(strict_types=1);

namespace App\Article\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractArticleEvent extends AbstractUserEvent
{
    public function __construct(
        int $articleId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('articleId', $articleId);
    }

    public function getArticleId(): int
    {
        return (int) $this->getProperty('articleId', 0);
    }
}
