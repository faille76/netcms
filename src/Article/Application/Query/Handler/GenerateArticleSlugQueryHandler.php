<?php

declare(strict_types=1);

namespace App\Article\Application\Query\Handler;

use App\Article\Application\Query\GenerateArticleSlugQuery;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use Cocur\Slugify\SlugifyInterface;

final class GenerateArticleSlugQueryHandler implements QueryHandlerInterface
{
    private ArticleDataProviderInterface $articleDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        ArticleDataProviderInterface $articleDataProvider,
        SlugifyInterface $slugify
    ) {
        $this->articleDataProvider = $articleDataProvider;
        $this->slugify = $slugify;
    }

    public function __invoke(GenerateArticleSlugQuery $query): string
    {
        $slug = $this->slugify->slugify($query->getName());

        if ($this->articleDataProvider->getArticleBySlug($slug) === null) {
            return $slug;
        }

        $base = $slug;
        $i = 1;
        do {
            $slug = $base . '-' . $i;
            $i++;
        } while ($this->articleDataProvider->getArticleBySlug($slug) !== null);

        return $slug;
    }
}
