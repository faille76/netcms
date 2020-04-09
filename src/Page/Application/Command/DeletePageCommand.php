<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeletePageCommand extends AbstractCommand
{
    private int $pageId;

    public function __construct(int $pageId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->pageId = $pageId;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
