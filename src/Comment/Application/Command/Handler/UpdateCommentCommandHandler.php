<?php

declare(strict_types=1);

namespace App\Comment\Application\Command\Handler;

use App\Article\Application\Event\ArticleCommentUpdatedEvent;
use App\Comment\Application\Command\UpdateCommentCommand;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Persister\CommentDataPersisterInterface;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Gallery\Application\Event\AlbumCommentUpdatedEvent;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateCommentCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateCommentCommand $command): void
    {
        $comment = $this->commentDataProvider->getCommentById($command->getCommentId());
        if ($comment !== null) {
            $this->commentDataPersister->updateComment($command->getCommentId(), $command->getContent());
            switch ($comment->getType()) {
                case CommentTypeEnum::ARTICLE_COMMENT:
                    $this->eventDispatcher->dispatch(new ArticleCommentUpdatedEvent(
                        $comment->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
                case CommentTypeEnum::ALBUM_COMMENT:
                    $this->eventDispatcher->dispatch(new AlbumCommentUpdatedEvent(
                        $comment->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
            }
        }
    }
}
