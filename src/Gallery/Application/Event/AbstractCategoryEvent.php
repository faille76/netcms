<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractCategoryEvent extends AbstractUserEvent
{
    public function __construct(int $categoryId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('categoryId', $categoryId);
    }

    public function getCategoryId(): int
    {
        return (int) $this->getProperty('categoryId', 0);
    }
}
