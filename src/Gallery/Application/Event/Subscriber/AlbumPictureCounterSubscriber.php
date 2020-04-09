<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event\Subscriber;

use App\Gallery\Application\Event\AlbumImageDeletedEvent;
use App\Gallery\Application\Event\AlbumImageUploadedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AlbumPictureCounterSubscriber implements EventSubscriberInterface
{
    private AlbumDataPersisterInterface $albumDataPersister;

    public function __construct(AlbumDataPersisterInterface $albumDataPersister)
    {
        $this->albumDataPersister = $albumDataPersister;
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
        $this->albumDataPersister->decreasePictureNumber($event->getAlbumId());
    }

    public function onImageAdded(AlbumImageUploadedEvent $event): void
    {
        $this->albumDataPersister->increasePictureNumber($event->getAlbumId());
    }
}
