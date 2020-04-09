<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreatePageCommand extends AbstractCommand
{
    private string $name;
    private int $parentId;
    private bool $enabled;

    public function __construct(string $name, int $parentId, bool $enabled, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->name = $name;
        $this->parentId = $parentId;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
