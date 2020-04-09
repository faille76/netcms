<?php

declare(strict_types=1);

namespace App\Article\Domain\Provider;

use App\Article\Domain\Article;
use App\Article\Domain\ArticleCollection;
use App\Shared\Domain\Criteria;

interface ArticleDataProviderInterface
{
    public function getArticleById(int $articleId): ?Article;

    public function getArticleBySlug(string $slug): ?Article;

    public function findArticles(?Criteria $criteria = null): ArticleCollection;

    public function findForSearch(array $keys): ArticleCollection;

    public function countArticles(): int;
}
