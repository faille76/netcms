<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Shared\Domain\Criteria;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListArticlesController extends AbstractController
{
    public const NB_ARTICLE_PAGE = 5;

    private ArticleDataProviderInterface $articleDataProvider;

    public function __construct(
        ArticleDataProviderInterface $articleDataProvider
    ) {
        $this->articleDataProvider = $articleDataProvider;
    }

    /**
     * @Route("/news", name="news", methods={"GET"})
     * @Route("/news/{pageNumber}", name="news_page", methods={"GET"})
     */
    public function __invoke(int $pageNumber = 1): Response
    {
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
                ->setMaxPerPage(self::NB_ARTICLE_PAGE)
                ->setCurrentPage($pageNumber)
            ;
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException();
        }

        return $this->render('article/list.html.twig', [
            'articles' => $pagerfanta->getCurrentPageResults(),
            'page_cur' => $pageNumber,
            'page_number' => $pagerfanta->getNbPages(),
        ]);
    }
}
