<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\Page\Application\Command\UpdateImgPageCommand;
use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assertion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdateImagePageController extends AbstractController
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
     * @Route("/admin/pages/{pageId}/images/{imgId}", name="admin_page_image_update", methods={"POST"})
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

        $imageName = $request->request->get('name', null);

        $render = [];

        try {
            Assertion::notEmpty($imageName, 'admin.page.image.error.name_empty');
            $this->commandBus->dispatch(new UpdateImgPageCommand(
                $imgId,
                $pageId,
                $imageName,
                $this->getUserId(),
                time()
            ));
            $render['succeed'] = 'admin.page.image.confirm.image_updated';
        } catch (\InvalidArgumentException $e) {
            $errorList = [$e->getMessage()];
            $render['errors'] = $errorList;
        }

        $images = $this->imagePageDataProvider->findImagesByPageId($pageId);

        return $this->render('admin/page_image_list.html.twig', array_merge([
            'page' => $page,
            'images' => $images,
        ], $render));
    }
}
