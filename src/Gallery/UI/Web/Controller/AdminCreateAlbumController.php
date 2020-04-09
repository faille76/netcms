<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\CreateAlbumCommand;
use App\Gallery\Application\Query\GetCategoriesTreeQuery;
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

final class AdminCreateAlbumController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/admin/gallery/albums/create", name="admin_gallery_album_create_post", methods={"POST"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $albumForm = [
            'name' => $request->request->get('name', null),
            'parent_id' => $request->request->getInt('parent_id', 0),
        ];

        $render = [];

        try {
            $this->validate($albumForm);
            $album = $this->handleCommand(new CreateAlbumCommand(
                $albumForm['name'],
                $albumForm['parent_id'],
                true,
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($album, 'admin.gallery.album.create.error.internal_error');
            $render['succeed'] = 'admin.gallery.album.create.confirm.album_created';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('admin/gallery_album_create.html.twig', array_merge([
            'name' => $albumForm['name'],
            'parent_id' => $albumForm['parent_id'],
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
        ], $render));
    }

    /**
     * @Route("/admin/gallery/albums/create", name="admin_gallery_album_create", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/gallery_album_create.html.twig', [
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $album): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($album['name'])
            ->notEmpty('admin.gallery.album.create.error.name_empty')
        ;
        $lazyAssert->verifyNow();
    }
}
