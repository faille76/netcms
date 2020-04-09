<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateAlbumCommand extends AbstractCommand
{
    private int $albumId;
    private string $name;
    private int $categoryId;

    public function __construct(
        int $albumId,
        string $name,
        int $categoryId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->albumId = $albumId;
        $this->name = $name;
        $this->categoryId = $categoryId;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
