<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateCategoryCommand extends AbstractCommand
{
    private int $categoryId;
    private string $name;
    private int $parentId;

    public function __construct(
        int $categoryId,
        string $name,
        int $parentId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }
}
