<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateEnableAlbumCommand extends AbstractCommand
{
    private int $albumId;
    private bool $enabled;

    public function __construct(
        int $albumId,
        bool $enabled,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->albumId = $albumId;
        $this->enabled = $enabled;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
