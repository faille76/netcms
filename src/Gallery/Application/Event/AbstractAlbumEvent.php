<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event;

use App\Shared\Application\Event\AbstractUserEvent;

abstract class AbstractAlbumEvent extends AbstractUserEvent
{
    public function __construct(int $albumId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->addProperty('albumId', $albumId);
    }

    public function getAlbumId(): int
    {
        return (int) $this->getProperty('albumId', 0);
    }
}
