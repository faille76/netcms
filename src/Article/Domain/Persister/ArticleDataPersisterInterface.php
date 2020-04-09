<?php

declare(strict_types=1);

namespace App\Article\Domain\Persister;

interface ArticleDataPersisterInterface
{
    public function createArticle(
        string $name,
        string $content,
        ?string $image,
        string $slug,
        int $authorId
    ): int;

    public function updateArticle(
        int $articleId,
        string $name,
        string $content,
        ?string $image
    ): void;

    public function deleteArticle(int $articleId): void;

    public function increaseArticleView(int $articleId): void;

    public function increaseCommentNumber(int $articleId): void;
    public function decreaseCommentNumber(int $articleId): void;
}
