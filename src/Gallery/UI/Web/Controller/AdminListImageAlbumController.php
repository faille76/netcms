<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListImageAlbumController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
    }

    /**
     * @Route("/admin/gallery/albums/{albumId}/pictures", name="admin_gallery_album_pictures_list", methods={"GET"})
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

        $pictures = $this->pictureDataProvider->findPicturesByAlbumId($album->getId());

        return $this->render('admin/gallery_album_pictures.html.twig', [
            'album' => $album,
            'pictures' => $pictures,
        ]);
    }
}
