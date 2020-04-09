<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Application\Event\ArticleSeenEvent;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Comment\Domain\CommentCollection;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Text;
use App\Shared\UI\Web\Controller\UserTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GetArticleController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private ArticleDataProviderInterface $articleDataProvider;
    private CommentDataProviderInterface $commentDataProvider;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ArticleDataProviderInterface $articleDataProvider,
        CommentDataProviderInterface $commentDataProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->articleDataProvider = $articleDataProvider;
        $this->commentDataProvider = $commentDataProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/article/{slug}", name="article_by_slug", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug, string $message = null): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        $article = $this->articleDataProvider->getArticleBySlug($slug);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        $this->eventDispatcher->dispatch(new ArticleSeenEvent(
            $article->getId(),
            $this->getUserIp($request),
            $this->getUser() ? $this->getUserId() : null,
            time()
        ));

        return $this->render('article/index.html.twig', [
            'title' => $article->getName(),
            'article' => $article,
            'comments' => $this->getComments($article->getId()),
            'content' => Text::fromString($article->getContent())->closeHtmlTags(),
            'message' => $message,
        ]);
    }

    private function getComments(int $articleId): CommentCollection
    {
        return $this->commentDataProvider->findCommentByArticleId(
            $articleId,
            CommentTypeEnum::ARTICLE_COMMENT,
            new Criteria(
                ['id' => 'DESC']
            )
        );
    }
}
