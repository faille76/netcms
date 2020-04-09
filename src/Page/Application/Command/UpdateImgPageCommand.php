<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateImgPageCommand extends AbstractCommand
{
    private int $id;
    private int $pageId;
    private string $name;

    public function __construct(int $id, int $pageId, string $name, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->id = $id;
        $this->pageId = $pageId;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
