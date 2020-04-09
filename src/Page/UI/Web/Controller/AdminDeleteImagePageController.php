<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Command\DeleteImgPageCommand;
use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteImagePageController extends AbstractController
{
    use UserTrait;

    private ImagePageDataProviderInterface $imagePageDataProvider;
    private PageDataProviderInterface $pageDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        ImagePageDataProviderInterface $imagePageDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->imagePageDataProvider = $imagePageDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/pages/{pageId}/images/{imgId}", name="admin_page_image_delete", methods={"GET"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request, int $pageId, int $imgId): Response
    {
        $page = $this->pageDataProvider->getPageById($pageId, $request->getLocale());
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        $image = $this->imagePageDataProvider->getImageByIdAndPageId($imgId, $pageId);
        if ($image === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new DeleteImgPageCommand(
            $imgId,
            $pageId,
            $this->getUserId(),
            time()
        ));

        $images = $this->imagePageDataProvider->findImagesByPageId($pageId);

        return $this->render('admin/page_image_list.html.twig', [
            'page' => $page,
            'images' => $images,
            'succeed' => 'admin.page.image.post.image_deleted',
        ]);
    }
}
