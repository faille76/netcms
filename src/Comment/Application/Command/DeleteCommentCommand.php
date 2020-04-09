<?php

declare(strict_types=1);

namespace App\Comment\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteCommentCommand extends AbstractCommand
{
    private int $commentId;

    public function __construct(int $commentId, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->commentId = $commentId;
    }

    public function getCommentId(): int
    {
        return $this->commentId;
    }
}
