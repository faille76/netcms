<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\UpdateDefaultImageCommand;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDefaultImageAlbumController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/gallery/albums/{albumId}/pictures/{pictureId}/default", name="admin_gallery_album_pictures_default", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(Request $request, int $albumId, int $pictureId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumById($albumId);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $picture = $this->pictureDataProvider->getPicture($pictureId);
        if ($picture === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new UpdateDefaultImageCommand(
            $albumId,
            $pictureId,
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_gallery_album_pictures_list.lang', [
            '_locale' => $request->getLocale(),
            'albumId' => $albumId,
        ]);
    }
}
