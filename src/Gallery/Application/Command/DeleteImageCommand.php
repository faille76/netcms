<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteImageCommand extends AbstractCommand
{
    private int $albumId;
    private int $pictureId;

    public function __construct(int $albumId, int $pictureId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->albumId = $albumId;
        $this->pictureId = $pictureId;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    public function getPictureId(): int
    {
        return $this->pictureId;
    }
}
