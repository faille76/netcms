<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event;

final class AlbumCreatedEvent extends AbstractAlbumEvent
{
    public function __construct(
        int $albumId,
        string $slug,
        int $parentId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($albumId, $userId, $occurredOn);
        $this->addProperties([
            'slug' => $slug,
            'parentId' => $parentId,
        ]);
    }

    public function getParentId(): int
    {
        return (int) $this->getProperty('parentId', 0);
    }

    public function getSlug(): string
    {
        return (string) $this->getProperty('slug', 'null');
    }
}
