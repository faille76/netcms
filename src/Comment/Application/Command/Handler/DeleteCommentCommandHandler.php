<?php

declare(strict_types=1);

namespace App\Comment\Application\Command\Handler;

use App\Article\Application\Event\ArticleCommentDeletedEvent;
use App\Comment\Application\Command\DeleteCommentCommand;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Persister\CommentDataPersisterInterface;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Gallery\Application\Event\AlbumCommentDeletedEvent;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteCommentCommandHandler implements CommandHandlerInterface
{
    private CommentDataPersisterInterface $commentDataPersister;
    private CommentDataProviderInterface $commentDataProvider;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CommentDataPersisterInterface $commentDataPersister,
        CommentDataProviderInterface $commentDataProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commentDataPersister = $commentDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->commentDataProvider = $commentDataProvider;
    }

    public function __invoke(DeleteCommentCommand $command): void
    {
        $comment = $this->commentDataProvider->getCommentById($command->getCommentId());
        if ($comment !== null) {
            $this->commentDataPersister->deleteCommentById($command->getCommentId());
            switch ($comment->getType()) {
                case CommentTypeEnum::ARTICLE_COMMENT:
                    $this->eventDispatcher->dispatch(new ArticleCommentDeletedEvent(
                        $comment->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
                case CommentTypeEnum::ALBUM_COMMENT:
                    $this->eventDispatcher->dispatch(new AlbumCommentDeletedEvent(
                        $comment->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
            }
        }
    }
}
