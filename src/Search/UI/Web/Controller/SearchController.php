<?php

declare(strict_types=1);

namespace App\Search\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SearchController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private ArticleDataProviderInterface $articleDataProvider;
    private PageDataProviderInterface $pageDataProvider;
    private DocumentDataProviderInterface $documentDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ArticleDataProviderInterface $articleDataProvider,
        PageDataProviderInterface $pageDataProvider,
        DocumentDataProviderInterface $documentDataProvider,
        AlbumDataProviderInterface $albumDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->articleDataProvider = $articleDataProvider;
        $this->pageDataProvider = $pageDataProvider;
        $this->documentDataProvider = $documentDataProvider;
        $this->albumDataProvider = $albumDataProvider;
    }

    /**
     * @Route("/search", name="search_post", methods={"POST"})
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::SEARCH)) {
            throw $this->createNotFoundException();
        }

        $keysText = $request->request->get('keys', null);
        if ($keysText === null || empty($keysText)) {
            return $this->render('search/index.html.twig', [
                'state' => false,
            ]);
        }
        $keys = str_replace([';', ',', '-', '|', '.'], ' ', $keysText);
        $keys = explode(' ', $keys);

        return $this->render('search/index.html.twig', [
            'keys' => $keysText,
            'articles' => $this->articleDataProvider->findForSearch($keys),
            'pages' => $this->pageDataProvider->findForSearch($keys, $request->getLocale()),
            'documents' => $this->documentDataProvider->findForSearch($keys),
            'albums' => $this->albumDataProvider->findForSearch($keys),
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::SEARCH)) {
            throw $this->createNotFoundException();
        }

        return $this->render('search/index.html.twig', [
            'state' => true,
        ]);
    }
}
