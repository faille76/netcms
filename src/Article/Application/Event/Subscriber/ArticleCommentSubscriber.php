<?php

declare(strict_types=1);

namespace App\Article\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleCommentDeletedEvent;
use App\Article\Application\Event\ArticleCommentedEvent;
use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ArticleCommentSubscriber implements EventSubscriberInterface
{
    private ArticleDataPersisterInterface $articleDataPersister;

    public function __construct(ArticleDataPersisterInterface $articleDataPersister)
    {
        $this->articleDataPersister = $articleDataPersister;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleCommentedEvent::class => 'onCommentAdded',
            ArticleCommentDeletedEvent::class => 'onCommentDeleted',
        ];
    }

    public function onCommentDeleted(ArticleCommentDeletedEvent $event): void
    {
        $this->articleDataPersister->decreaseCommentNumber($event->getArticleId());
    }

    public function onCommentAdded(ArticleCommentedEvent $event): void
    {
        $this->articleDataPersister->increaseCommentNumber($event->getArticleId());
    }
}
