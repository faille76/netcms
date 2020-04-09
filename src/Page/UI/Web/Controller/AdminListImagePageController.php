<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListImagePageController extends AbstractController
{
    private ImagePageDataProviderInterface $imagePageDataProvider;
    private PageDataProviderInterface $pageDataProvider;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        ImagePageDataProviderInterface $imagePageDataProvider
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->imagePageDataProvider = $imagePageDataProvider;
    }

    /**
     * @Route("/admin/pages/{pageId}/images", name="admin_page_image_list", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request, int $pageId): Response
    {
        $page = $this->pageDataProvider->getPageById($pageId, $request->getLocale());
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        $images = $this->imagePageDataProvider->findImagesByPageId($pageId);

        return $this->render('admin/page_image_list.html.twig', [
            'page' => $page,
            'images' => $images,
        ]);
    }
}
