<?php

declare(strict_types=1);

namespace App\Comment\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleDeletedEvent;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Persister\CommentDataPersisterInterface;
use App\Gallery\Application\Event\AlbumDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ObjectDeletedSubscriber implements EventSubscriberInterface
{
    private CommentDataPersisterInterface $commentDataPersister;

    public function __construct(CommentDataPersisterInterface $commentDataPersister)
    {
        $this->commentDataPersister = $commentDataPersister;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleDeletedEvent::class => 'onDeleteArticleComment',
            AlbumDeletedEvent::class => 'onDeleteAlbumComment',
        ];
    }

    public function onDeleteArticleComment(ArticleDeletedEvent $event): void
    {
        $this->commentDataPersister->deleteCommentsByArticle(
            $event->getArticleId(),
            CommentTypeEnum::ARTICLE_COMMENT
        );
    }

    public function onDeleteAlbumComment(AlbumDeletedEvent $event): void
    {
        $this->commentDataPersister->deleteCommentsByArticle(
            $event->getAlbumId(),
            CommentTypeEnum::ALBUM_COMMENT
        );
    }
}
