<?php

declare(strict_types=1);

namespace App\Comment\Domain\Provider;

use App\Comment\Domain\Comment;
use App\Comment\Domain\CommentCollection;
use App\Shared\Domain\Criteria;

interface CommentDataProviderInterface
{
    public function getCommentById(int $commentId): ?Comment;

    public function findComments(
        ?int $commentType,
        ?Criteria $criteria = null
    ): CommentCollection;

    public function findCommentByArticleId(
        int $articleId,
        int $commentType,
        ?Criteria $criteria = null
    ): CommentCollection;

    public function findCommentsByUserId(
        int $userId,
        ?Criteria $criteria = null
    ): CommentCollection;
}
