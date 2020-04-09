<?php

declare(strict_types=1);

namespace App\Article\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteArticleCommand extends AbstractCommand
{
    private int $articleId;

    public function __construct(
        int $articleId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->articleId = $articleId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }
}
