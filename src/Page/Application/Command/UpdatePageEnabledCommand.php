<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdatePageEnabledCommand extends AbstractCommand
{
    private int $pageId;
    private bool $enabled;

    public function __construct(int $pageId, bool $enabled, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->pageId = $pageId;
        $this->enabled = $enabled;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
