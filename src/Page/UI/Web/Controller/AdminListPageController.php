<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Domain\Page;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Domain\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListPageController extends AbstractController
{
    private PageDataProviderInterface $pageDataProvider;

    public function __construct(
        PageDataProviderInterface $pageDataProvider
    ) {
        $this->pageDataProvider = $pageDataProvider;
    }

    /**
     * @Route("/admin/pages", name="admin_page_list", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request): Response
    {
        $pages = $this->pageDataProvider->findPagesByParentId(
            null,
            $request->getLocale(),
            null,
            new Criteria(['id' => 'DESC'])
        );

        $namePage = [
            0 => '-',
        ];
        /** @var Page $page */
        foreach ($pages as $page) {
            $namePage[$page->getId()] = $page->getTitle();
        }

        return $this->render('admin/page_list.html.twig', [
            'pages' => $pages,
            'pageNames' => $namePage,
        ]);
    }
}
