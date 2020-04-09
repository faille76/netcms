<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\DeleteImageCommand;
use App\Gallery\Application\Event\AlbumImageDeletedEvent;
use App\Gallery\Domain\AlbumPicture;
use App\Gallery\Domain\Persister\PictureDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteImageCommandHandler implements CommandHandlerInterface
{
    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataProviderInterface $pictureDataProvider;
    private PictureDataPersisterInterface $pictureDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataProviderInterface $pictureDataProvider,
        PictureDataPersisterInterface $pictureDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataProvider = $pictureDataProvider;
        $this->pictureDataPersister = $pictureDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(DeleteImageCommand $command): void
    {
        $album = $this->albumDataProvider->getAlbumById($command->getAlbumId());
        if ($album === null) {
            throw new NotFoundException('Album ' . $command->getAlbumId() . ' does not exists.');
        }
        $picture = $this->pictureDataProvider->getPicture($command->getPictureId());
        if ($picture === null) {
            throw new NotFoundException('Picture ' . $command->getPictureId() . ' does not exists.');
        }

        $this->physicalDataPersister->remove($album->getRelativePath() . '/' . $picture->getName());
        foreach (AlbumPicture::FILTERS as $filterType) {
            $this->physicalDataPersister->remove(
                $album->getRelativePath() . '/' . FilterTypeEnum::getFilterName($filterType) . '/' . $picture->getName()
            );
        }

        $this->pictureDataPersister->deletePicture($command->getPictureId());

        $this->eventDispatcher->dispatch(new AlbumImageDeletedEvent(
            $command->getAlbumId(),
            $command->getPictureId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
