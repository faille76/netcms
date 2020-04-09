<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Query\GetSitemapQuery;
use App\Page\Domain\Page;
use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Domain\ImageCollection;
use App\Shared\Domain\Text;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends AbstractController
{
    use QueryHandleTrait;

    private PageDataProviderInterface $pageDataProvider;
    private ImagePageDataProviderInterface $imagePageDataProvider;
    private string $locale;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        ImagePageDataProviderInterface $imagePageDataProvider,
        MessageBusInterface $queryBus,
        string $defaultLocale
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->imagePageDataProvider = $imagePageDataProvider;
        $this->locale = $defaultLocale;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/page/{pageName}/{pageName2}/{pageName3}", name="page_sub_sub_by_slug")
     */
    public function getSubSubPage(Request $request, string $pageName, string $pageName2, string $pageName3): Response
    {
        $this->locale = $request->getLocale();
        $page = $this->getPage($pageName3, $pageName);
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('page/index.html.twig',
            array_merge($page, ['page_name' => $pageName])
        );
    }

    /**
     * @Route("/page/{pageName}/{pageName2}", name="page_sub_by_slug")
     */
    public function getSubPage(Request $request, string $pageName, string $pageName2): Response
    {
        $this->locale = $request->getLocale();
        $page = $this->getPage($pageName2, $pageName);
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('page/index.html.twig',
            array_merge($page, ['page_name' => $pageName])
        );
    }

    /**
     * @Route("/page/{pageName}", name="page_by_slug")
     */
    public function __invoke(Request $request, string $pageName): Response
    {
        $this->locale = $request->getLocale();
        $page = $this->getPage($pageName);
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('page/index.html.twig',
            array_merge($page, ['page_name' => $pageName])
        );
    }

    private function getSubPages(
        string $pageName,
        int $pageId,
        string $pageOrigin = null
    ): array {
        $pageCollection = $this->pageDataProvider->findPagesByParentId($pageId, $this->locale, true);

        $sub = [];
        /** @var Page $page */
        foreach ($pageCollection as $page) {
            $sub[$page->getId()] = [
                'link' => ($pageOrigin !== null)
                    ? $this->generateUrl('page_sub_sub_by_slug.lang', [
                        'pageName' => $pageOrigin,
                        'pageName2' => $pageName,
                        'pageName3' => $page->getSlug(),
                        '_locale' => $this->locale,
                    ])
                    : $this->generateUrl('page_sub_by_slug.lang', [
                        'pageName' => $pageName,
                        'pageName2' => $page->getSlug(),
                        '_locale' => $this->locale,
                    ]),
                'name' => $page->getTitle(),
            ];
        }

        return $sub;
    }

    private function getImages(int $pageId): ImageCollection
    {
        return $this->imagePageDataProvider->findImagesByPageId($pageId);
    }

    private function getPage(string $pageName, string $pageOrigin = null): ?array
    {
        $page = $this->pageDataProvider->getPageBySlug($pageName, $this->locale, true);
        if ($page === null) {
            return null;
        }

        $imageList = $this->getImages($page->getId());

        return [
            'title' => $page->getTitle(),
            'sub_page' => $this->getSubPages(
                $page->getSlug(),
                $page->getId(),
                $pageOrigin
            ),
            'images' => $imageList,
            'slug' => $page->getSlug(),
            'text' => ((!empty($page->getContent())) ? Text::fromString($page->getContent())->closeHtmlTags() : null),
            'sitemap' => $this->handleQuery(new GetSitemapQuery($page->getParentId(), $this->locale)),
        ];
    }
}
