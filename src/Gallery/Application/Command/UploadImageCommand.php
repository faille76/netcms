<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadImageCommand extends AbstractCommand
{
    private int $albumId;
    private UploadedFile $file;

    public function __construct(int $albumId, UploadedFile $file, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->albumId = $albumId;
        $this->file = $file;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
