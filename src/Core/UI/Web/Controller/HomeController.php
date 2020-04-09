<?php

declare(strict_types=1);

namespace App\Core\UI\Web\Controller;

use App\Article\Domain\ArticleCollection;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Comment\Domain\Comment;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Gallery\Domain\Album;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Partner\Domain\PartnerCollection;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\Domain\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    private ArticleDataProviderInterface $articleDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;
    private PartnerDataProviderInterface $partnerDataProvider;
    private CommentDataProviderInterface $commentDataProvider;
    private CategoryDataProviderInterface $categoryDataProvider;

    public function __construct(
        ArticleDataProviderInterface $articleDataProvider,
        AlbumDataProviderInterface $albumDataProvider,
        PartnerDataProviderInterface $partnerDataProvider,
        CommentDataProviderInterface $commentDataProvider,
        CategoryDataProviderInterface $categoryDataProvider
    ) {
        $this->articleDataProvider = $articleDataProvider;
        $this->albumDataProvider = $albumDataProvider;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->commentDataProvider = $commentDataProvider;
        $this->categoryDataProvider = $categoryDataProvider;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function __invoke(): Response
    {
        return $this->render('home/index.html.twig', [
            'articles' => $this->getArticles(),
            'albums' => $this->getAlbums(),
            'comments' => $this->getComments(),
            'partners' => $this->getPartners(),
        ]);
    }

    public function getArticles(): ArticleCollection
    {
        return $this->articleDataProvider->findArticles(new Criteria(['id' => 'DESC'], 0, 4));
    }

    public function getAlbums(): array
    {
        $albums = $this->albumDataProvider->findAlbumsFromCategoryId(null, true, new Criteria(['id' => 'DESC'], 0, 4));

        $albumList = [];
        $memoize = [];
        /** @var Album $album */
        foreach ($albums as $album) {
            if (!isset($memoize[$album->getParentId()])) {
                $memoize[$album->getParentId()] = null;
                $category = $this->categoryDataProvider->getCategoryById($album->getParentId());
                if ($category !== null) {
                    $memoize[$album->getParentId()] = [
                        'name' => $category->getName(),
                        'slug' => $category->getSlug(),
                    ];
                }
            }

            $albumList[] = [
                'category' => $memoize[$album->getParentId()],
                'relativePath' => $album->getRelativePath(),
                'pictureCover' => $album->getPictureCover(),
                'name' => $album->getName(),
                'slug' => $album->getSlug(),
                'createdAt' => $album->getCreatedAt(),
            ];
        }

        return $albumList;
    }

    public function getComments(): array
    {
        $comments = $this->commentDataProvider->findComments(null, new Criteria(['id' => 'DESC'], 0, 4));

        $commentList = [];
        $memoize = [];
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            if (!isset($memoize[$comment->getType()][$comment->getArticleId()])) {
                switch ($comment->getType()) {
                    case CommentTypeEnum::ARTICLE_COMMENT:
                        $article = $this->articleDataProvider->getArticleById($comment->getArticleId());
                        if ($article === null) {
                            break;
                        }
                        $memoize[$comment->getType()][$comment->getArticleId()] = [
                            'name' => $article->getName(),
                            'slug' => $article->getSlug(),
                        ];

                        break;
                    case CommentTypeEnum::ALBUM_COMMENT:
                        $album = $this->albumDataProvider->getAlbumById($comment->getArticleId());
                        if ($album === null) {
                            break;
                        }
                        $memoize[$comment->getType()][$comment->getArticleId()] = [
                            'name' => $album->getName(),
                            'slug' => $album->getSlug(),
                        ];

                        break;
                }
            }

            if (!isset($memoize[$comment->getType()][$comment->getArticleId()])) {
                continue;
            }
            $commentList[] = [
                'article' => $memoize[$comment->getType()][$comment->getArticleId()],
                'type' => $comment->getType(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor(),
                'createdAt' => $comment->getCreatedAt(),
            ];
        }

        return $commentList;
    }

    public function getPartners(): PartnerCollection
    {
        return $this->partnerDataProvider->findPartners(true);
    }
}
