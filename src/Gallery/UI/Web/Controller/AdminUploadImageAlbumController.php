<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\UploadImageCommand;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUploadImageAlbumController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;
    private AlbumDataProviderInterface $albumDataProvider;

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
     * @Route("/admin/gallery/albums/{albumId}/pictures", name="admin_gallery_album_pictures_upload", methods={"POST"})
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

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        foreach ($request->files->get('pictures') as $file) {
            if ($file->isValid()) {
                if (!in_array($file->getMimeType(), MimeTypeEnum::toArray())) {
                    continue;
                }
                $this->handleCommand(new UploadImageCommand(
                    $album->getId(),
                    $file,
                    $this->getUserId(),
                    time()
                ));
            }
        }

        return $this->redirectToRoute('admin_gallery_album_pictures_list.lang', [
            '_locale' => $request->getLocale(),
            'albumId' => $albumId,
        ]);
    }
}
