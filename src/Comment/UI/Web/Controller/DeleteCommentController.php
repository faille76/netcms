<?php

declare(strict_types=1);

namespace App\Comment\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Comment\Application\Command\DeleteCommentCommand;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteCommentController extends AbstractController
{
    use UserTrait;

    private CommentDataProviderInterface $commentDataProvider;
    private FeatureFlippingInterface $featureFlipping;
    private ArticleDataProviderInterface $articleDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        CommentDataProviderInterface $commentDataProvider,
        ArticleDataProviderInterface $articleDataProvider,
        AlbumDataProviderInterface $albumDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->commentDataProvider = $commentDataProvider;
        $this->featureFlipping = $featureFlipping;
        $this->articleDataProvider = $articleDataProvider;
        $this->albumDataProvider = $albumDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/comments/{commentId}/delete", name="comment_delete", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(Request $request, int $commentId): Response
    {
        $comment = $this->commentDataProvider->getCommentById($commentId);
        if ($comment === null) {
            throw $this->createNotFoundException();
        }

        switch ($comment->getType()) {
            case CommentTypeEnum::ARTICLE_COMMENT:
                $article = $this->articleDataProvider->getArticleById($comment->getArticleId());
                if ($article === null) {
                    throw $this->createNotFoundException();
                }
                $redirectTo = $this->redirectToRoute('article_by_slug.lang', [
                    'slug' => $article->getSlug(),
                    '_locale' => $request->getLocale(),
                ]);

                break;

            case CommentTypeEnum::ALBUM_COMMENT:
                $album = $this->albumDataProvider->getAlbumById($comment->getArticleId());
                if ($album === null) {
                    throw $this->createNotFoundException();
                }
                $redirectTo = $this->redirectToRoute('gallery_album_by_slug.lang', [
                    'slug' => $album->getSlug(),
                    '_locale' => $request->getLocale(),
                ]);

                break;

            default:
                $redirectTo = $this->redirectToRoute('home.lang', [
                    '_locale' => $request->getLocale(),
                ]);

                break;
        }

        $this->commandBus->dispatch(new DeleteCommentCommand(
            $commentId,
            $this->getUserId(),
            time()
        ));

        return $redirectTo;
    }
}
