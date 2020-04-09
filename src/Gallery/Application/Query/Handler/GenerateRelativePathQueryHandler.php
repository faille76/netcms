<?php

declare(strict_types=1);

namespace App\Gallery\Application\Query\Handler;

use App\Gallery\Application\Query\GenerateRelativePathQuery;
use App\Gallery\Domain\AlbumPicture;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Query\Handler\QueryHandlerInterface;

final class GenerateRelativePathQueryHandler implements QueryHandlerInterface
{
    private CategoryDataProviderInterface $categoryDataProvider;

    public function __construct(CategoryDataProviderInterface $categoryDataProvider)
    {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    public function __invoke(GenerateRelativePathQuery $query): string
    {
        $category = $this->categoryDataProvider->getCategoryById($query->getCategoryId());
        if ($category === null) {
            $basePath = AlbumPicture::PATH_BASE;
        } else {
            $basePath = $category->getRelativePath();
        }

        return $basePath . $query->getSlug() . '/';
    }
}
