<?php

declare(strict_types=1);

namespace App\Comment\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class CreateCommentCommand extends AbstractCommand
{
    private int $articleId;
    private int $commentType;
    private string $content;

    public function __construct(
        int $articleId,
        int $commentType,
        string $content,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->articleId = $articleId;
        $this->commentType = $commentType;
        $this->content = $content;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getCommentType(): int
    {
        return $this->commentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
