<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Application\Command\UpdateArticleCommand;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdateArticleController extends AbstractController
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
     * @Route("/admin/articles/{articleId}/update", name="admin_article_update_post", methods={"POST"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function post(Request $request, int $articleId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        $article = $this->articleDataProvider->getArticleById($articleId);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        $articleForm = [
            'name' => $request->request->get('name', null),
            'image' => $request->files->get('image', null),
            'text' => $request->request->get('text', null),
        ];

        $render = [];

        try {
            $this->validate($articleForm);
            $this->commandBus->dispatch(new UpdateArticleCommand(
                $article->getId(),
                $articleForm['name'],
                $articleForm['image'],
                $articleForm['text'],
                $this->getUserId(),
                time()
            ));
            $render['succeed'] = 'admin.article.create.confirm.article_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/article_create.html.twig', array_merge([
            'name' => $articleForm['name'],
            'image' => $article->getImage(),
            'text' => $articleForm['text'],
            'succeed' => 'admin.article.create.confirm.article_updated',
        ], $render));
    }

    /**
     * @Route("/admin/articles/{articleId}/update", name="admin_article_update", methods={"GET"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function __invoke(int $articleId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        $article = $this->articleDataProvider->getArticleById($articleId);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/article_create.html.twig', [
            'title' => $article->getName(),
            'image' => $article->getImage(),
            'text' => $article->getContent(),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $article): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($article['name'])
            ->notEmpty('admin.article.create.error.name_empty')
            ->that($article['text'])
            ->notEmpty('admin.article.create.error.text_empty')
        ;
        if ($article['image'] !== null) {
            $lazyAssert->that($article['image']->isValid())->true('admin.article.create.error.image_invalid');
            $lazyAssert->that($article['image']->getMimeType())->inArray(MimeTypeEnum::toArray(), 'admin.article.create.error.image_invalid');
        }
        $lazyAssert->verifyNow();
    }
}
