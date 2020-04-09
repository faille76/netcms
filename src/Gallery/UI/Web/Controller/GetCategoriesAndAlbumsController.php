<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Query\GetAlbumsOfCategoryQuery;
use App\Gallery\Application\Query\GetSitemapQuery;
use App\Gallery\Application\Query\GetSubCategoriesOfCategoryQuery;
use App\Gallery\Domain\Category;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class GetCategoriesAndAlbumsController extends AbstractController
{
    use QueryHandleTrait;

    private FeatureFlippingInterface $featureFlipping;
    private CategoryDataProviderInterface $categoryDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        CategoryDataProviderInterface $categoryDataProvider,
        MessageBusInterface $queryBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/picture", name="gallery_category_default", methods={"GET"})
     * @Route("/picture/{slug}", name="gallery_category_by_slug", methods={"GET"})
     */
    public function __invoke(string $slug = null): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $category = $this->getCurrentCategory($slug);

        return $this->render('gallery/category.html.twig', [
            'title' => $category !== null ? $category->getName() : null,
            'current' => $category,
            'categories' => $this->handleQuery(new GetSubCategoriesOfCategoryQuery($category)),
            'albums' => $this->handleQuery(new GetAlbumsOfCategoryQuery($category)),
            'sitemap' => $category !== null ? $this->handleQuery(new GetSitemapQuery($category->getParentId())) : [],
        ]);
    }

    private function getCurrentCategory(string $slug = null): ?Category
    {
        if ($slug !== null) {
            return $this->categoryDataProvider->getCategoryBySlug($slug);
        }

        return null;
    }
}
