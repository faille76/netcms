<?php

declare(strict_types=1);

namespace App\Article\Application\Command\Handler;

use App\Article\Application\Command\CreateArticleCommand;
use App\Article\Application\Command\UploadArticleImageCommand;
use App\Article\Application\Event\ArticleCreatedEvent;
use App\Article\Application\Query\GenerateArticleSlugQuery;
use App\Article\Domain\Article;
use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateArticleCommandHandler implements CommandHandlerInterface
{
    use QueryHandleTrait;
    use CommandHandleTrait;

    private ArticleDataPersisterInterface $articleDataPersister;
    private ArticleDataProviderInterface $articleDataProvider;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ArticleDataPersisterInterface $articleDataPersister,
        ArticleDataProviderInterface $articleDataProvider,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->articleDataPersister = $articleDataPersister;
        $this->articleDataProvider = $articleDataProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    public function __invoke(CreateArticleCommand $command): ?Article
    {
        $slug = $this->handleQuery(new GenerateArticleSlugQuery($command->getName()));

        $image = $command->getImage() !== null ? $this->handleCommand(new UploadArticleImageCommand(
            $command->getImage(),
            $slug,
            $command->getUserId(),
            $command->getOccurredOn()
        )) : null;

        $articleId = $this->articleDataPersister->createArticle(
            $command->getName(),
            $command->getText(),
            $image,
            $slug,
            $command->getUserId()
        );
        $article = $this->articleDataProvider->getArticleById($articleId);
        if ($article instanceof Article) {
            $this->eventDispatcher->dispatch(
                new ArticleCreatedEvent(
                    $article->getId(),
                    $article->getName(),
                    $article->getImage(),
                    $article->getSlug(),
                    $command->getUserId(),
                    $command->getOccurredOn()
                )
            );
        }

        return $article;
    }
}
