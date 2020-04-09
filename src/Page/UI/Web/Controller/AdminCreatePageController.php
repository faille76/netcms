<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Command\CreatePageCommand;
use App\Page\Application\Command\CreatePageLangCommand;
use App\Page\Application\Query\GetPagesTreeQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
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

final class AdminCreatePageController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;
    use QueryHandleTrait;

    private PageDataProviderInterface $pageDataProvider;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/admin/pages/create", name="admin_page_create_post", methods={"POST"})
     * @IsGranted("ROLE_PAGES")
     */
    public function post(Request $request): Response
    {
        $pagesTree = $this->handleQuery(new GetPagesTreeQuery($request->getLocale()));
        $pageForm = [
            'parent_id' => $request->request->getInt('parent_id', 0),
            'name_fr' => $request->request->get('name_fr', null),
            'name_en' => $request->request->get('name_en', null),
            'content_fr' => $request->request->get('content_fr', null),
            'content_en' => $request->request->get('content_en', null),
        ];

        $render = [];

        try {
            $this->validate($pageForm);
            $pageId = $this->handleCommand(
                new CreatePageCommand(
                    $pageForm['name_fr'],
                    $pageForm['parent_id'],
                    true,
                    $this->getUserId(),
                    time()
                )
            );
            Assertion::greaterThan($pageId, 0, 'admin.page.create.error.internal_error');
            $this->commandBus->dispatch(new CreatePageLangCommand(
                $pageId,
                'fr',
                $pageForm['name_fr'],
                $pageForm['content_fr'],
                $this->getUserId(),
                time()
            ));
            $this->commandBus->dispatch(new CreatePageLangCommand(
                $pageId,
                'en',
                $pageForm['name_en'],
                $pageForm['content_en'],
                $this->getUserId(),
                time()
            ));
            $page = $this->pageDataProvider->getPageById($pageId, $request->getLocale());
            if ($page === null) {
                throw new \InvalidArgumentException('admin.page.create.error.internal_error');
            }
            $render['succeed'] = 'admin.page.create.confirm.page_created';
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
            'name_fr' => $pageForm['name_fr'],
            'name_en' => $pageForm['name_en'],
            'content_fr' => $pageForm['content_fr'],
            'content_en' => $pageForm['content_en'],
            'parent_id' => $pageForm['parent_id'],
            'page_tree' => $pagesTree,
        ], $render));
    }

    /**
     * @Route("/admin/pages/create", name="admin_page_create", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request): Response
    {
        $pagesTree = $this->handleQuery(new GetPagesTreeQuery($request->getLocale()));

        return $this->render('admin/page_create.html.twig', [
            'page_tree' => $pagesTree,
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $page): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($page['name_fr'])
            ->notEmpty('admin.page.create.error.name_fr_empty')
            ->that($page['name_en'])
            ->notEmpty('admin.page.create.error.name_en_empty')
        ;
        $lazyAssert->verifyNow();
    }
}
