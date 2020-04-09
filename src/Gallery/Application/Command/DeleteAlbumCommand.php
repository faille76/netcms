<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteAlbumCommand extends AbstractCommand
{
    private int $albumId;

    public function __construct(
        int $albumId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->albumId = $albumId;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }
}
