<?php

declare(strict_types=1);

namespace App\Article\Infrastructure\Storage\Mysql;

use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class ArticleDataPersister extends AbstractDataPersister implements ArticleDataPersisterInterface
{
    public function createArticle(
        string $name,
        string $content,
        ?string $image,
        string $slug,
        int $authorId
    ): int {
        $this->insert('articles', [
            'name' => $name,
            'content' => $content,
            'image' => $image,
            'slug' => $slug,
            'author_id' => $authorId,
        ]);

        return $this->getLastInsertId();
    }

    public function updateArticle(
        int $articleId,
        string $name,
        string $content,
        ?string $image
    ): void {
        $this->update('articles', [
            'name' => $name,
            'content' => $content,
            'image' => $image,
        ], [
            'id' => $articleId,
        ]);
    }

    public function deleteArticle(int $articleId): void
    {
        $this->delete('articles', [
            'id' => $articleId,
        ]);
    }

    public function increaseArticleView(int $articleId): void
    {
        $this->executeUpdate('UPDATE articles SET view = view + 1 WHERE id = :id', [
            'id' => $articleId,
        ]);
    }

    public function increaseCommentNumber(int $articleId): void
    {
        $this->executeUpdate('UPDATE articles SET nb_comments = nb_comments + 1 WHERE id = :id', [
            'id' => $articleId,
        ]);
    }

    public function decreaseCommentNumber(int $articleId): void
    {
        $this->executeUpdate('UPDATE articles SET nb_comments = nb_comments - 1 WHERE id = :id', [
            'id' => $articleId,
        ]);
    }
}
