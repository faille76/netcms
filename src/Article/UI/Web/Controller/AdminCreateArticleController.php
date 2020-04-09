<?php

declare(strict_types=1);

namespace App\Article\UI\Web\Controller;

use App\Article\Application\Command\CreateArticleCommand;
use App\Article\Domain\Article;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\Assertion;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminCreateArticleController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/articles/create", name="admin_article_create_post", methods={"POST"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
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
            /** @var Article $article */
            $article = $this->handleCommand(new CreateArticleCommand(
                $articleForm['name'],
                $articleForm['image'],
                $articleForm['text'],
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($article, 'admin.article.create.error.internal_error');
            $render['succeed'] = 'admin.article.create.confirm.article_created';
            $render['image'] = $article->getImage();
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('admin/article_create.html.twig', array_merge([
            'name' => $articleForm['name'],
            'text' => $articleForm['text'],
        ], $render));
    }

    /**
     * @Route("/admin/articles/create", name="admin_article_create", methods={"GET"})
     * @IsGranted("ROLE_ARTICLES")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::ARTICLE)) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/article_create.html.twig');
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
