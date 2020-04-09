<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\DeleteAlbumCommand;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteAlbumController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private AlbumDataProviderInterface $albumDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        AlbumDataProviderInterface $albumDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->albumDataProvider = $albumDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/gallery/albums/{albumId}/delete", name="admin_gallery_album_delete", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(Request $request, int $albumId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumById($albumId);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new DeleteAlbumCommand(
            $album->getId(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_gallery_album_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
