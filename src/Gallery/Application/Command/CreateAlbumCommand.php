<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreateAlbumCommand extends AbstractCommand
{
    private string $name;
    private int $categoryId;
    private bool $enabled;

    public function __construct(
        string $name,
        int $categoryId,
        bool $enabled,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
