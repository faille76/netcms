<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\UpdateEnableAlbumCommand;
use App\Gallery\Application\Event\AlbumUpdatedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateEnableAlbumCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateEnableAlbumCommand $command): void
    {
        $this->albumDataPersister->updateAlbumEnabled($command->getAlbumId(), $command->isEnabled());

        $this->eventDispatcher->dispatch(new AlbumUpdatedEvent(
            $command->getAlbumId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
