<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\UpdateAlbumCommand;
use App\Gallery\Application\Query\GetCategoriesTreeQuery;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
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

final class AdminUpdateAlbumController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;

    private AlbumDataProviderInterface $albumDataProvider;
    private FeatureFlippingInterface $featureFlipping;
    private MessageBusInterface $commandBus;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/admin/gallery/albums/{albumId}/update", name="admin_gallery_album_update_post", methods={"POST"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function post(Request $request, int $albumId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumById($albumId);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $albumForm = [
            'name' => $request->request->get('name', null),
            'parent_id' => $request->request->getInt('parent_id', 0),
        ];

        $render = [];

        try {
            $this->validate($albumForm);
            $this->commandBus->dispatch(
                new UpdateAlbumCommand(
                    $album->getId(),
                    $albumForm['name'],
                    $albumForm['parent_id'],
                    $this->getUserId(),
                    time()
                )
            );
            $render['succeed'] = 'admin.gallery.album.create.confirm.album_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/gallery_album_create.html.twig', array_merge([
            'name' => $albumForm['name'],
            'parent_id' => $albumForm['parent_id'],
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
        ], $render));
    }

    /**
     * @Route("/admin/gallery/albums/{albumId}/update", name="admin_gallery_album_update", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(int $albumId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumById($albumId);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/gallery_album_create.html.twig', [
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
            'name' => $album->getName(),
            'parent_id' => $album->getParentId(),
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
