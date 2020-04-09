<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\DeleteAlbumCommand;
use App\Gallery\Application\Event\AlbumDeletedEvent;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Gallery\Domain\Persister\PictureDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteAlbumCommandHandler implements CommandHandlerInterface
{
    private AlbumDataProviderInterface $albumDataProvider;
    private AlbumDataPersisterInterface $albumDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PhysicalDataPersisterInterface $physicalDataPersister;
    private PictureDataPersisterInterface $pictureDataPersister;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        AlbumDataPersisterInterface $albumDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        PictureDataPersisterInterface $pictureDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->albumDataPersister = $albumDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->albumDataProvider = $albumDataProvider;
        $this->physicalDataPersister = $physicalDataPersister;
        $this->pictureDataPersister = $pictureDataPersister;
    }

    public function __invoke(DeleteAlbumCommand $command): void
    {
        $album = $this->albumDataProvider->getAlbumById($command->getAlbumId());
        if ($album === null) {
            throw new NotFoundException('Album with id "' . $command->getAlbumId() . '" was not found.');
        }

        $this->physicalDataPersister->removeDir($album->getRelativePath());

        $this->pictureDataPersister->deletePicturesByAlbumId($command->getAlbumId());

        $this->albumDataPersister->deleteAlbum($command->getAlbumId());

        $this->eventDispatcher->dispatch(new AlbumDeletedEvent(
            $command->getAlbumId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
