<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Command\UpdatePageLangCommand;
use App\Page\Application\Command\UpdateParentPageCommand;
use App\Page\Application\Query\GetPagesTreeQuery;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdatePageController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;

    private PageDataProviderInterface $pageDataProvider;
    private MessageBusInterface $commandBus;

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
     * @Route("/admin/pages/{pageId}/update", name="admin_page_update_post", methods={"POST"})
     * @IsGranted("ROLE_PAGES")
     */
    public function post(Request $request, int $pageId): Response
    {
        $pageFr = $this->pageDataProvider->getPageById($pageId, 'fr');
        $pageEn = $this->pageDataProvider->getPageById($pageId, 'en');
        if ($pageFr === null || $pageEn === null) {
            throw $this->createNotFoundException();
        }
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
            if ($pageFr->getParentId() !== $pageForm['parent_id']) {
                $this->commandBus->dispatch(new UpdateParentPageCommand(
                    $pageId,
                    $pageForm['parent_id'],
                    $this->getUserId(),
                    time()
                ));
            }
            if ($pageFr->getTitle() !== $pageForm['name_fr'] || $pageFr->getContent() !== $pageForm['content_fr']) {
                $this->commandBus->dispatch(new UpdatePageLangCommand(
                    $pageId,
                    'fr',
                    $pageForm['name_fr'],
                    $pageForm['content_fr'],
                    $this->getUserId(),
                    time()
                ));
            }
            if ($pageEn->getTitle() !== $pageForm['name_en'] || $pageEn->getContent() !== $pageForm['content_en']) {
                $this->commandBus->dispatch(new UpdatePageLangCommand(
                    $pageId,
                    'en',
                    $pageForm['name_en'],
                    $pageForm['content_en'],
                    $this->getUserId(),
                    time()
                ));
            }
            $render['succeed'] = 'admin.page.create.confirm.page_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/page_create.html.twig', array_merge([
            'name_fr' => $pageForm['name_fr'],
            'name_en' => $pageForm['name_en'],
            'content_fr' => $pageForm['content_fr'],
            'content_en' => $pageForm['content_en'],
            'parent_id' => $pageForm['parent_id'],
            'page_tree' => $pagesTree,
        ], $render));
    }

    /**
     * @Route("/admin/pages/{pageId}/update", name="admin_page_update", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request, int $pageId): Response
    {
        $pagesTree = $this->handleQuery(new GetPagesTreeQuery($request->getLocale()));
        $pageFr = $this->pageDataProvider->getPageById($pageId, 'fr');
        $pageEn = $this->pageDataProvider->getPageById($pageId, 'en');
        if ($pageFr === null || $pageEn === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/page_create.html.twig', [
            'name_fr' => $pageFr->getTitle(),
            'name_en' => $pageEn->getTitle(),
            'content_fr' => $pageFr->getContent(),
            'content_en' => $pageEn->getContent(),
            'parent_id' => $pageFr->getParentId(),
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
