<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteCategoryCommand extends AbstractCommand
{
    private int $categoryId;

    public function __construct(
        int $categoryId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->categoryId = $categoryId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
