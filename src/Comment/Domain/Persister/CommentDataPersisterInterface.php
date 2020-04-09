<?php

declare(strict_types=1);

namespace App\Comment\Domain\Persister;

interface CommentDataPersisterInterface
{
    public function addComment(
        int $articleId,
        int $commentType,
        string $content,
        int $userId
    ): int;

    public function updateComment(int $commentId, string $content): void;

    public function deleteCommentsByArticle(int $articleId, int $commentType): void;

    public function deleteCommentById(int $commentId): void;
}
