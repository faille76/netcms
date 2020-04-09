<?php

declare(strict_types=1);

namespace App\Page\Application\Query\Handler;

use App\Page\Application\Query\GeneratePageSlugQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use Cocur\Slugify\SlugifyInterface;

final class GeneratePageSlugQueryHandler implements QueryHandlerInterface
{
    private PageDataProviderInterface $pageDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        SlugifyInterface $slugify
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->slugify = $slugify;
    }

    public function __invoke(GeneratePageSlugQuery $query): string
    {
        $slug = $this->slugify->slugify($query->getName());

        if ($this->pageDataProvider->getPageBySlug($slug, 'fr') === null) {
            return $slug;
        }

        $base = $slug;
        $i = 1;
        do {
            $slug = $base . '-' . $i;
            $i++;
        } while ($this->pageDataProvider->getPageBySlug($slug, 'fr') !== null);

        return $slug;
    }
}
