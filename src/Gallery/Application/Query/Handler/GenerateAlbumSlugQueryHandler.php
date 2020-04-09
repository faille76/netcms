<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GenerateAlbumSlugQuery;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use Cocur\Slugify\SlugifyInterface;

final class GenerateAlbumSlugQueryHandler implements QueryHandlerInterface
{
    private AlbumDataProviderInterface $albumDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        SlugifyInterface $slugify
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->slugify = $slugify;
    }

    public function __invoke(GenerateAlbumSlugQuery $query): string
    {
        $slug = $this->slugify->slugify($query->getName());

        if ($this->albumDataProvider->getAlbumBySlug($slug) === null) {
            return $slug;
        }

        $base = $slug;
        $i = 1;
        do {
            $slug = $base . '-' . $i;
            $i++;
        } while ($this->albumDataProvider->getAlbumBySlug($slug) !== null);

        return $slug;
    }
}
