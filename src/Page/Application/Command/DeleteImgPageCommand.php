<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteImgPageCommand extends AbstractCommand
{
    private int $id;
    private int $pageId;

    public function __construct(int $id, int $pageId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->id = $id;
        $this->pageId = $pageId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
