<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event\Subscriber;

use App\Gallery\Application\Event\AlbumCommentDeletedEvent;
use App\Gallery\Application\Event\AlbumCommentedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AlbumCommentSubscriber implements EventSubscriberInterface
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
            AlbumCommentedEvent::class => 'onCommentAdded',
            AlbumCommentDeletedEvent::class => 'onCommentDeleted',
        ];
    }

    public function onCommentDeleted(AlbumCommentDeletedEvent $event): void
    {
        $this->albumDataPersister->decreaseCommentNumber($event->getAlbumId());
    }

    public function onCommentAdded(AlbumCommentedEvent $event): void
    {
        $this->albumDataPersister->increaseCommentNumber($event->getAlbumId());
    }
}
