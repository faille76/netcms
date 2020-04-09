<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Command\UpdatePageEnabledCommand;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminEnablePageController extends AbstractController
{
    use UserTrait;

    private PageDataProviderInterface $pageDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/pages/{pageId}/enable", name="admin_page_enable", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request, int $pageId): Response
    {
        $page = $this->pageDataProvider->getPageById($pageId, $request->getLocale());
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new UpdatePageEnabledCommand(
            $page->getId(),
            !$page->isEnabled(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_page_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
