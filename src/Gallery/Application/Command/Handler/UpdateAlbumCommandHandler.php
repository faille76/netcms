<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\UpdateAlbumCommand;
use App\Gallery\Application\Event\AlbumUpdatedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateAlbumCommandHandler implements CommandHandlerInterface
{
    private AlbumDataPersisterInterface $albumDataPersister;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        AlbumDataPersisterInterface $albumDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->albumDataPersister = $albumDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateAlbumCommand $command): void
    {
        $this->albumDataPersister->updateAlbum($command->getAlbumId(), $command->getName(), $command->getCategoryId());

        $this->eventDispatcher->dispatch(new AlbumUpdatedEvent(
            $command->getAlbumId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
