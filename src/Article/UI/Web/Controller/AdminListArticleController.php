<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\Domain\Criteria;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListArticleController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private ArticleDataProviderInterface $articleDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ArticleDataProviderInterface $articleDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->articleDataProvider = $articleDataProvider;
    }

    /**
     * @Route("/admin/articles", name="admin_article_list", methods={"GET"})
     * @Route("/admin/articles/{pageNumber}", name="admin_article_page_list", methods={"GET"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function __invoke(int $pageNumber = 1): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        $pagerfanta = new Pagerfanta(new class($this->articleDataProvider) implements AdapterInterface {
            private ArticleDataProviderInterface $articleDataProvider;

            public function __construct(ArticleDataProviderInterface $articleDataProvider)
            {
                $this->articleDataProvider = $articleDataProvider;
            }

            public function getNbResults()
            {
                return $this->articleDataProvider->countArticles();
            }

            public function getSlice($offset, $length)
            {
                return $this->articleDataProvider->findArticles(
                    new Criteria(['id' => 'DESC'], $offset, $length)
                );
            }
        });

        try {
            $pagerfanta
                ->setMaxPerPage(30)
                ->setCurrentPage($pageNumber)
            ;
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/article_list.html.twig', [
            'articles' => $pagerfanta->getCurrentPageResults(),
            'page_number' => $pagerfanta->getNbPages(),
            'page_cur' => $pagerfanta->getCurrentPage(),
        ]);
    }
}
