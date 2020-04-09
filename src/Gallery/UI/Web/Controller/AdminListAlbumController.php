<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Domain\Category;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Domain\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListAlbumController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private CategoryDataProviderInterface $categoryDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        CategoryDataProviderInterface $categoryDataProvider,
        AlbumDataProviderInterface $albumDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->albumDataProvider = $albumDataProvider;
    }

    /**
     * @Route("/admin/gallery/albums", name="admin_gallery_album_list", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $categories = $this->categoryDataProvider->findCategoriesByParentId(
            null,
            null,
            new Criteria(['id' => 'DESC'])
        );

        $nameCategory = [
            0 => '-',
        ];
        /** @var Category $category */
        foreach ($categories as $category) {
            $nameCategory[$category->getId()] = $category->getName();
        }

        $albums = $this->albumDataProvider->findAlbumsFromCategoryId(
            null,
            null,
            new Criteria(['id' => 'DESC'])
        );

        return $this->render('admin/gallery_album_list.html.twig', [
            'albums' => $albums,
            'categoriesNames' => $nameCategory,
        ]);
    }
}
