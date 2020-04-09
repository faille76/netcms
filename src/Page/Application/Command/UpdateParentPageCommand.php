<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateParentPageCommand extends AbstractCommand
{
    private int $pageId;
    private int $parentId;

    public function __construct(int $pageId, int $parentId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->pageId = $pageId;
        $this->parentId = $parentId;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }
}
