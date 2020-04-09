<?php

declare(strict_types=1);

namespace App\Comment\Application\Event\Subscriber;

use App\Comment\Application\Command\DeleteCommentCommand;
use App\Comment\Domain\Comment;
use App\Comment\Domain\Provider\CommentDataProviderInterface;
use App\User\Application\Event\UserDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class UserDeletedSubscriber implements EventSubscriberInterface
{
    private CommentDataProviderInterface $commentDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        CommentDataProviderInterface $commentDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->commentDataProvider = $commentDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            UserDeletedEvent::class => 'onUserDeleted',
        ];
    }

    public function onUserDeleted(UserDeletedEvent $event): void
    {
        $comments = $this->commentDataProvider->findCommentsByUserId($event->getUserId());
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $this->commandBus->dispatch(new DeleteCommentCommand(
                $comment->getId(),
                $event->getUserId(),
                $event->getOccurredOn()
            ));
        }
    }
}
