<?php

declare(strict_types=1);

namespace App\Core\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleCreatedEvent;
use App\Article\Application\Event\ArticleDeletedEvent;
use App\Article\Application\Event\ArticleUpdatedEvent;
use App\Contact\Application\Event\ContactCreatedEvent;
use App\Contact\Application\Event\ContactDeletedEvent;
use App\Contact\Application\Event\ContactUpdatedEvent;
use App\Document\Application\Event\DocumentCreatedEvent;
use App\Document\Application\Event\DocumentDeletedEvent;
use App\Document\Application\Event\DocumentUpdatedEvent;
use App\Gallery\Application\Event\AlbumCreatedEvent;
use App\Gallery\Application\Event\AlbumDeletedEvent;
use App\Gallery\Application\Event\AlbumImageDeletedEvent;
use App\Gallery\Application\Event\AlbumImageUploadedEvent;
use App\Gallery\Application\Event\AlbumUpdatedEvent;
use App\Gallery\Application\Event\CategoryCreatedEvent;
use App\Gallery\Application\Event\CategoryDeletedEvent;
use App\Gallery\Application\Event\CategoryUpdatedEvent;
use App\Page\Application\Event\PageContentUpdatedEvent;
use App\Page\Application\Event\PageCreatedEvent;
use App\Page\Application\Event\PageDeletedEvent;
use App\Page\Application\Event\PageUpdatedEvent;
use App\Partner\Application\Event\PartnerCreatedEvent;
use App\Partner\Application\Event\PartnerDeletedEvent;
use App\Partner\Application\Event\PartnerUpdatedEvent;
use App\Shared\Application\Event\AbstractUserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array_fill_keys([
            // Articles
            ArticleCreatedEvent::class,
            ArticleDeletedEvent::class,
            ArticleUpdatedEvent::class,

            // Contact
            ContactCreatedEvent::class,
            ContactUpdatedEvent::class,
            ContactDeletedEvent::class,

            // Documents
            DocumentCreatedEvent::class,
            DocumentUpdatedEvent::class,
            DocumentDeletedEvent::class,

            // Album
            AlbumCreatedEvent::class,
            AlbumDeletedEvent::class,
            AlbumUpdatedEvent::class,
            AlbumImageDeletedEvent::class,
            AlbumImageUploadedEvent::class,

            // Album category
            CategoryCreatedEvent::class,
            CategoryDeletedEvent::class,
            CategoryUpdatedEvent::class,

            // Partners
            PartnerDeletedEvent::class,
            PartnerUpdatedEvent::class,
            PartnerCreatedEvent::class,

            // Page
            PageContentUpdatedEvent::class,
            PageCreatedEvent::class,
            PageDeletedEvent::class,
            PageUpdatedEvent::class,
        ], 'onLogger');
    }

    public function onLogger(AbstractUserEvent $event): void
    {
        $this->logger->notice(json_encode($event));
    }
}
