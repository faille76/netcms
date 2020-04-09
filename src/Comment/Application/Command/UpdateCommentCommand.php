<?php

declare(strict_types=1);

namespace App\Comment\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateCommentCommand extends AbstractCommand
{
    private int $commentId;
    private string $content;

    public function __construct(int $commentId, string $content, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->commentId = $commentId;
        $this->content = $content;
    }

    public function getCommentId(): int
    {
        return $this->commentId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
