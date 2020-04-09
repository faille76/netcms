<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event;

final class AlbumImageUploadedEvent extends AbstractAlbumEvent
{
    public function __construct(int $albumId, int $pictureId, int $userId, int $occurredOn)
    {
        parent::__construct($albumId, $userId, $occurredOn);
        $this->addProperty('pictureId', $pictureId);
    }

    public function getPictureId(): int
    {
        return (int) $this->getProperty('pictureId', 0);
    }
}
