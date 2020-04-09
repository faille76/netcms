<?php

declare(strict_types=1);

namespace App\Comment\Application\Command\Handler;

use App\Article\Application\Event\ArticleCommentedEvent;
use App\Comment\Application\Command\CreateCommentCommand;
use App\Comment\Domain\Comment;
use App\Comment\Domain\CommentTypeEnum;
use App\Comment\Domain\Persister\CommentDataPersisterInterface;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\Gallery\Application\Event\AlbumCommentedEvent;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateCommentCommandHandler implements CommandHandlerInterface
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

    public function __invoke(CreateCommentCommand $command): ?Comment
    {
        $commentId = $this->commentDataPersister->addComment(
            $command->getArticleId(),
            $command->getCommentType(),
            $command->getContent(),
            $command->getUserId()
        );

        $comment = $this->commentDataProvider->getCommentById($commentId);
        if ($comment !== null) {
            switch ($command->getCommentType()) {
                case CommentTypeEnum::ARTICLE_COMMENT:
                    $this->eventDispatcher->dispatch(new ArticleCommentedEvent(
                        $command->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
                case CommentTypeEnum::ALBUM_COMMENT:
                    $this->eventDispatcher->dispatch(new AlbumCommentedEvent(
                        $command->getArticleId(),
                        $command->getUserId(),
                        $command->getOccurredOn()
                    ));

                    break;
            }
        }

        return $comment;
    }
}
