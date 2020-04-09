<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Application\Command\DeleteArticleCommand;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteArticleController extends AbstractController
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
     * @Route("/admin/articles/{articleId}/delete", name="admin_article_delete", methods={"GET"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function __invoke(Request $request, int $articleId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        $article = $this->articleDataProvider->getArticleById($articleId);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new DeleteArticleCommand(
            $article->getId(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_article_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
