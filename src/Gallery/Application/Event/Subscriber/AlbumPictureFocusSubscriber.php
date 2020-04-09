<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event\Subscriber;

use App\Gallery\Application\Event\AlbumImageDeletedEvent;
use App\Gallery\Application\Event\AlbumImageUploadedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AlbumPictureFocusSubscriber implements EventSubscriberInterface
{
    private AlbumDataProviderInterface $albumDataProvider;
    private AlbumDataPersisterInterface $albumDataPersister;
    private PictureDataProviderInterface $pictureDataProvider;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        AlbumDataPersisterInterface $albumDataPersister,
        PictureDataProviderInterface $pictureDataProvider
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->albumDataPersister = $albumDataPersister;
        $this->pictureDataProvider = $pictureDataProvider;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AlbumImageUploadedEvent::class => 'onImageAdded',
            AlbumImageDeletedEvent::class => 'onImageDeleted',
        ];
    }

    public function onImageDeleted(AlbumImageDeletedEvent $event): void
    {
        $album = $this->albumDataProvider->getAlbumById($event->getAlbumId());
        if ($album === null) {
            throw new NotFoundException('Album id ' . $event->getAlbumId() . ' was not found.');
        }

        if ($album->getPictureCover() !== null && $album->getPictureCover()->getId() === $event->getPictureId()) {
            $picture = $this->pictureDataProvider->findOneRandomPicture($event->getAlbumId());
            $pictureId = null;
            if ($picture !== null) {
                $pictureId = $picture->getId();
            }
            $this->albumDataPersister->updateDefaultImageAlbum($album->getId(), $pictureId);
        }
    }

    public function onImageAdded(AlbumImageUploadedEvent $event): void
    {
        $album = $this->albumDataProvider->getAlbumById($event->getAlbumId());
        if ($album === null) {
            throw new NotFoundException('Album id ' . $event->getAlbumId() . ' was not found.');
        }

        if ($album->getPictureCover() === null) {
            $this->albumDataPersister->updateDefaultImageAlbum($album->getId(), $event->getPictureId());
        }
    }
}
