<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Comment\Application\Command\CreateCommentCommand;
use App\Comment\Domain\CommentTypeEnum;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateArticleCommentController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private ArticleDataProviderInterface $articleDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ArticleDataProviderInterface $articleDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->articleDataProvider = $articleDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/article/{slug}", name="comment_article_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        if (
            !$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)
            ||
            !$this->featureFlipping->isModuleEnabled(FeatureEnum::COMMENT_ARTICLE)
        ) {
            throw $this->createNotFoundException();
        }

        $article = $this->articleDataProvider->getArticleBySlug($slug);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new CreateCommentCommand(
            $article->getId(),
            CommentTypeEnum::ARTICLE_COMMENT,
            $request->request->get('message'),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('article_by_slug.lang', [
            'slug' => $slug,
            '_locale' => $request->getLocale(),
        ]);
    }
}
