<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event;

use App\Shared\Application\Event\AbstractBaseEvent;

final class AlbumSeenEvent extends AbstractBaseEvent
{
    public function __construct(
        int $albumId,
        string $ip,
        ?int $userId,
        int $occurredOn
    ) {
        parent::__construct($occurredOn);
        $this->addProperties([
            'albumId' => $albumId,
            'ip' => $ip,
            'userId' => $userId,
        ]);
    }

    public function getAlbumId(): int
    {
        return (int) $this->getProperty('albumId', 0);
    }

    public function getIp(): string
    {
        return (string) $this->getProperty('ip', 'null');
    }

    public function getUserId(): ?int
    {
        return $this->getProperty('userId', null);
    }
}
