<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateEnableCategoryCommand extends AbstractCommand
{
    private int $categoryId;
    private bool $enabled;

    public function __construct(
        int $categoryId,
        bool $enabled,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->categoryId = $categoryId;
        $this->enabled = $enabled;
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
