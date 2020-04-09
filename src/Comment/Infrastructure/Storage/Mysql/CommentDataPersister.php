<?php

declare(strict_types=1);

namespace App\Comment\Infrastructure\Storage\Mysql;

use App\Comment\Domain\Persister\CommentDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class CommentDataPersister extends AbstractDataPersister implements CommentDataPersisterInterface
{
    public function addComment(
        int $articleId,
        int $commentType,
        string $content,
        int $userId
    ): int {
        $qb = $this->createQueryBuilder()
            ->insert('comments')
            ->values([
                'article_id' => '?',
                'content' => '?',
                'user_id' => '?',
                'type' => '?',
            ])
            ->setParameter(0, $articleId, 'integer')
            ->setParameter(1, $content, 'string')
            ->setParameter(2, $userId, 'integer')
            ->setParameter(3, $commentType, 'integer')
        ;

        return $this->save($qb);
    }

    public function updateComment(int $commentId, string $content): void
    {
        $this->update('comments', [
            'content' => $content,
        ], [
            'id' => $commentId,
        ]);
    }

    public function deleteCommentsByArticle(int $articleId, int $commentType): void
    {
        $this->delete('comments', [
            'article_id' => $articleId,
            'type' => $commentType,
        ]);
    }

    public function deleteCommentById(int $commentId): void
    {
        $this->delete('comments', [
            'id' => $commentId,
        ]);
    }
}
