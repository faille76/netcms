<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreatePageLangCommand extends AbstractCommand
{
    private int $pageId;
    private string $lang;
    private string $name;
    private ?string $content;

    public function __construct(int $pageId, string $lang, string $name, ?string $content, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->pageId = $pageId;
        $this->lang = $lang;
        $this->name = $name;
        $this->content = $content;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
