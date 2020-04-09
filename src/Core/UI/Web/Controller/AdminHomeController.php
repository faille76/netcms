<?php

declare(strict_types=1);

namespace App\Core\UI\Web\Controller;

use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\User\Domain\Provider\UserDataProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminHomeController extends AbstractController
{
    private UserDataProviderInterface $userDataProvider;
    private ArticleDataProviderInterface $articleDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;
    private PartnerDataProviderInterface $partnerDataProvider;
    private DocumentDataProviderInterface $documentDataProvider;

    public function __construct(
        UserDataProviderInterface $userDataProvider,
        ArticleDataProviderInterface $articleDataProvider,
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider,
        PartnerDataProviderInterface $partnerDataProvider,
        DocumentDataProviderInterface $documentDataProvider
    ) {
        $this->userDataProvider = $userDataProvider;
        $this->articleDataProvider = $articleDataProvider;
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->documentDataProvider = $documentDataProvider;
    }

    /**
     * @Route("/admin", name="admin_home", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function __invoke(): Response
    {
        return $this->render('admin/index.html.twig', [
            'users_count' => $this->userDataProvider->countUsers(),
            'articles_count' => $this->articleDataProvider->countArticles(),
            'albums_count' => $this->albumDataProvider->countAlbums(),
            'pictures_count' => $this->pictureDataProvider->countPictures(),
            'partners_count' => $this->partnerDataProvider->countPartners(),
            'documents_count' => $this->documentDataProvider->countDocuments(),
        ]);
    }
}
