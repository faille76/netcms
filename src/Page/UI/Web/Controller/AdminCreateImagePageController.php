<?php

declare(strict_types=1);

namespace App\Page\UI\Web\Controller;

use App\ImageFactory\Domain\MimeTypeEnum;
use App\Page\Application\Command\CreateImgPageCommand;
use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
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

final class AdminCreateImagePageController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private ImagePageDataProviderInterface $imagePageDataProvider;
    private PageDataProviderInterface $pageDataProvider;

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
     * @Route("/admin/pages/{pageId}/images", name="admin_page_image_create", methods={"POST"})
     * @IsGranted("ROLE_PAGES")
     */
    public function __invoke(Request $request, int $pageId): Response
    {
        $page = $this->pageDataProvider->getPageById($pageId, $request->getLocale());
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        $imageForm = [
            'name' => $request->request->get('name', null),
            'image' => $request->files->get('image', null),
        ];

        $render = [];

        try {
            $this->validate($imageForm);
            $image = $this->handleCommand(new CreateImgPageCommand(
                $pageId,
                $imageForm['name'],
                $imageForm['image'],
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($image, 'admin.page.image.error.internal_error');
            $render['succeed'] = 'admin.page.image.confirm.image_added';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
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

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $page): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($page['name'])
            ->notEmpty('admin.page.image.error.name_empty')
            ->that($page['image'])
            ->notNull('admin.page.image.error.image_invalid')
        ;
        if ($page['image'] !== null) {
            $lazyAssert->that($page['image']->isValid())->true('admin.page.image.error.image_invalid');
            $lazyAssert->that($page['image']->getMimeType())->inArray(MimeTypeEnum::toArray(), 'admin.page.create.error.image_invalid');
        }
        $lazyAssert->verifyNow();
    }
}
