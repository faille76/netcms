<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\CreateAlbumCommand;
use App\Gallery\Application\Event\AlbumCreatedEvent;
use App\Gallery\Application\Query\GenerateAlbumSlugQuery;
use App\Gallery\Application\Query\GenerateRelativePathQuery;
use App\Gallery\Domain\Album;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateAlbumCommandHandler implements CommandHandlerInterface
{
    use QueryHandleTrait;

    private AlbumDataProviderInterface $albumDataProvider;

    private AlbumDataPersisterInterface $albumDataPersister;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        AlbumDataPersisterInterface $albumDataPersister,
        MessageBusInterface $queryBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->albumDataPersister = $albumDataPersister;
        $this->queryBus = $queryBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateAlbumCommand $command): ?Album
    {
        $slug = $this->handleQuery(new GenerateAlbumSlugQuery($command->getName()));
        $relativePath = $this->handleQuery(new GenerateRelativePathQuery($command->getCategoryId(), $slug));
        $albumId = $this->albumDataPersister->createAlbum(
            $command->getName(),
            $relativePath,
            $slug,
            $command->getCategoryId(),
            $command->isEnabled(),
            $command->getUserId()
        );
        $album = $this->albumDataProvider->getAlbumById($albumId);
        if ($album !== null) {
            $this->eventDispatcher->dispatch(new AlbumCreatedEvent(
                $albumId,
                $slug,
                $command->getCategoryId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $album;
    }
}
