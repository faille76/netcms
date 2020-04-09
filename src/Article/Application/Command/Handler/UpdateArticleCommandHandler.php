<?php

declare(strict_types=1);

namespace App\Article\Application\Command\Handler;

use App\Article\Application\Command\UpdateArticleCommand;
use App\Article\Application\Command\UploadArticleImageCommand;
use App\Article\Application\Event\ArticleUpdatedEvent;
use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateArticleCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private ArticleDataProviderInterface $articleDataProvider;
    private ArticleDataPersisterInterface $articleDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ArticleDataProviderInterface $articleDataProvider,
        ArticleDataPersisterInterface $articleDataPersister,
        MessageBusInterface $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->articleDataProvider = $articleDataProvider;
        $this->articleDataPersister = $articleDataPersister;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateArticleCommand $command): void
    {
        $article = $this->articleDataProvider->getArticleById($command->getArticleId());
        if ($article === null) {
            throw new NotFoundException('Article ' . $command->getArticleId() . ' does not exists.');
        }

        $image = $command->getImage() !== null ? $this->handleCommand(new UploadArticleImageCommand(
            $command->getImage(),
            $article->getSlug(),
            $command->getUserId(),
            $command->getOccurredOn()
        )) : $article->getImage();

        $this->articleDataPersister->updateArticle(
            $command->getArticleId(),
            $command->getName(),
            $command->getText(),
            $image
        );

        $this->eventDispatcher->dispatch(new ArticleUpdatedEvent(
            $command->getArticleId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
