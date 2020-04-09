<?php

declare(strict_types=1);

namespace App\Core\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleCommentDeletedEvent;
use App\Article\Application\Event\ArticleCommentedEvent;
use App\Article\Application\Event\ArticleCommentUpdatedEvent;
use App\Gallery\Application\Event\AlbumCommentDeletedEvent;
use App\Gallery\Application\Event\AlbumCommentedEvent;
use App\Gallery\Application\Event\AlbumCommentUpdatedEvent;
use App\Shared\Application\Event\AbstractUserEvent;
use App\User\Application\Event\SessionCreatedEvent;
use App\User\Application\Event\UserCreatedEvent;
use App\User\Application\Event\UserDeletedEvent;
use App\User\Application\Event\UserPasswordUpdatedEvent;
use App\User\Application\Event\UserUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserEventSubscriber implements EventSubscriberInterface
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
            // Article
            ArticleCommentedEvent::class,
            ArticleCommentDeletedEvent::class,
            ArticleCommentUpdatedEvent::class,

            // Album
            AlbumCommentedEvent::class,
            AlbumCommentDeletedEvent::class,
            AlbumCommentUpdatedEvent::class,

            // User
            SessionCreatedEvent::class,
            UserCreatedEvent::class,
            UserUpdatedEvent::class,
            UserPasswordUpdatedEvent::class,
            UserDeletedEvent::class,
        ], 'onLogger');
    }

    public function onLogger(AbstractUserEvent $event): void
    {
        $this->logger->notice(json_encode($event));
    }
}
