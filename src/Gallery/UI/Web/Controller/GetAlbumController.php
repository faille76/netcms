<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Event\AlbumSeenEvent;
use App\Gallery\Application\Query\GetSitemapQuery;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class GetAlbumController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;

    private FeatureFlippingInterface $featureFlipping;
    private CategoryDataProviderInterface $categoryDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;
    private CommentDataProviderInterface $commentDataProvider;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        CategoryDataProviderInterface $categoryDataProvider,
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider,
        CommentDataProviderInterface $commentDataProvider,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $queryBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
        $this->commentDataProvider = $commentDataProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/picture/album/{slug}", name="gallery_album_by_slug", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumBySlug($slug);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $this->eventDispatcher->dispatch(new AlbumSeenEvent(
            $album->getId(),
            $this->getUserIp($request),
            $this->getUser() ? $this->getUserId() : null,
            time()
        ));

        return $this->render('gallery/album.html.twig', [
            'title' => $album->getName(),
            'category' => $this->categoryDataProvider->getCategoryById($album->getParentId()),
            'album' => $album,
            'pictures' => $this->pictureDataProvider->findPicturesByAlbumId($album->getId()),
            'comments' => $this->commentDataProvider->findCommentByArticleId(
                $album->getId(),
                CommentTypeEnum::ALBUM_COMMENT,
                new Criteria(['id' => 'DESC'])
            ),
            'sitemap' => $this->handleQuery(new GetSitemapQuery($album->getParentId())),
        ]);
    }
}
