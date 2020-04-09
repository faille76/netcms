<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Comment\Application\Command\CreateCommentCommand;
use App\Comment\Domain\CommentTypeEnum;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateAlbumCommentController extends AbstractController
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
     * @Route("/picture/album/{slug}", name="comment_album_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)
            ||
            !$this->featureFlipping->isModuleEnabled(FeatureEnum::COMMENT_ALBUM)
        ) {
            throw $this->createNotFoundException();
        }

        $album = $this->albumDataProvider->getAlbumBySlug($slug);
        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new CreateCommentCommand(
            $album->getId(),
            CommentTypeEnum::ALBUM_COMMENT,
            $request->request->get('message'),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('gallery_album_by_slug.lang', [
            'slug' => $slug,
            '_locale' => $request->getLocale(),
        ]);
    }
}
