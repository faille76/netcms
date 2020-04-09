<?php

declare(strict_types=1);

namespace App\Gallery\Application\Event\Subscriber;

use App\Gallery\Application\Event\AlbumSeenEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\ViewCache\Domain\ViewerCacheDataPersisterInterface;
use App\ViewCache\Domain\ViewerCacheDataProviderInterface;
use App\ViewCache\Domain\ViewerTypeEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AlbumSeenSubscriber implements EventSubscriberInterface
{
    private ViewerCacheDataProviderInterface $viewerCacheDataProvider;
    private ViewerCacheDataPersisterInterface $viewerCacheDataPersister;
    private AlbumDataPersisterInterface $albumDataPersister;

    public function __construct(
        ViewerCacheDataProviderInterface $viewerCacheDataProvider,
        ViewerCacheDataPersisterInterface $viewerCacheDataPersister,
        AlbumDataPersisterInterface $albumDataPersister
    ) {
        $this->viewerCacheDataProvider = $viewerCacheDataProvider;
        $this->viewerCacheDataPersister = $viewerCacheDataPersister;
        $this->albumDataPersister = $albumDataPersister;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AlbumSeenEvent::class => 'onAlbumSeen',
        ];
    }

    public function onAlbumSeen(AlbumSeenEvent $event): void
    {
        if (!$this->viewerCacheDataProvider->hasViewed($event->getAlbumId(), ViewerTypeEnum::ALBUM, $event->getIp())) {
            $this->viewerCacheDataPersister->addViewer($event->getAlbumId(), ViewerTypeEnum::ALBUM, $event->getIp());
            $this->albumDataPersister->increaseAlbumView($event->getAlbumId());
        }

        if (
            $event->getUserId() !== null
            &&
            !$this->viewerCacheDataProvider->hasUserViewed($event->getAlbumId(), ViewerTypeEnum::ALBUM, $event->getUserId())
        ) {
            $this->viewerCacheDataPersister->addUserViewed($event->getAlbumId(), ViewerTypeEnum::ALBUM, $event->getUserId());
        }
    }
}
