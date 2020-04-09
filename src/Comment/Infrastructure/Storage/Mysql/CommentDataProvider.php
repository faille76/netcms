<?php

declare(strict_types=1);

namespace App\Comment\Infrastructure\Storage\Mysql;

use App\Comment\Domain\Comment;
use App\Comment\Domain\CommentCollection;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class CommentDataProvider extends AbstractDataProvider implements CommentDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getCommentById(int $commentId): ?Comment
    {
        $qb = $this->createCommentQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('comment.id = :id')
            ->setParameter('id', $commentId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createCommentFromRow($row);
    }

    public function findComments(
        ?int $commentType,
        ?Criteria $criteria = null
    ): CommentCollection {
        $commentCollection = new CommentCollection();

        $qb = $this->createCommentQueryBuilder();

        if ($commentType !== null) {
            $qb
                ->andWhere('comment.type = :comment_type')
                ->setParameter('comment_type', $commentType, 'integer')
            ;
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $commentCollection->add($this->createCommentFromRow($row));
        }

        return $commentCollection;
    }

    public function findCommentByArticleId(
        int $articleId,
        int $commentType,
        ?Criteria $criteria = null
    ): CommentCollection {
        $commentCollection = new CommentCollection();

        $qb = $this->createCommentQueryBuilder()
            ->andWhere('comment.type = :comment_type')
            ->andWhere('comment.article_id = :article_id')
            ->setParameter('comment_type', $commentType, 'integer')
            ->setParameter('article_id', $articleId, 'integer')
        ;
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $commentCollection->add($this->createCommentFromRow($row));
        }

        return $commentCollection;
    }

    public function findCommentsByUserId(
        int $userId,
        ?Criteria $criteria = null
    ): CommentCollection {
        $commentCollection = new CommentCollection();

        $qb = $this->createCommentQueryBuilder()
            ->andWhere('comment.user_id = :user_id')
            ->setParameter('user_id', $userId, 'integer')
        ;
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $commentCollection->add($this->createCommentFromRow($row));
        }

        return $commentCollection;
    }

    private function createCommentFromRow(array $row): Comment
    {
        $row['id'] = (int) $row['id'];
        $row['article_id'] = (int) $row['article_id'];
        $row['type'] = (int) $row['type'];
        $row['author'] = [
            'user_id' => (int) $row['author_id'],
            'first_name' => $row['author_first_name'],
            'last_name' => $row['author_last_name'],
            'avatar' => $row['author_avatar'],
        ];
        unset($row['author_id'], $row['author_first_name'], $row['author_last_name'], $row['author_avatar']);

        return Comment::fromArray($row);
    }

    private function createCommentQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'comment.id',
                'comment.content',
                'comment.article_id',
                'comment.created_at',
                'comment.type',
                'author.id as author_id',
                'author.first_name as author_first_name',
                'author.last_name as author_last_name',
                'author.avatar as author_avatar',
            ])
            ->from('comments', 'comment')
            ->innerJoin('comment', 'users', 'author', 'author.id = comment.user_id')
        ;
    }
}
