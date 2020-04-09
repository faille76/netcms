<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GetAlbumsOfCategoryQuery;
use App\Gallery\Domain\AlbumCollection;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;
use App\Shared\Domain\Criteria;

final class GetAlbumsOfCategoryQueryHandler implements QueryHandlerInterface
{
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
    }

    public function __invoke(GetAlbumsOfCategoryQuery $query): AlbumCollection
    {
        return $this->albumDataProvider->findAlbumsFromCategoryId(
            $query->getCategoryId(),
            true,
            new Criteria(['id' => 'DESC'])
        );
    }
}
