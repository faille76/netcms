<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\UploadImageCommand;
use App\Gallery\Application\Event\AlbumImageUploadedEvent;
use App\Gallery\Domain\AlbumPicture;
use App\Gallery\Domain\Persister\PictureDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\ImageFactory\Application\Command\ResizeImageCommand;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\ImageFactory\Domain\Image;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class UploadImageCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private AlbumDataProviderInterface $albumDataProvider;
    private PictureDataPersisterInterface $pictureDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        AlbumDataProviderInterface $albumDataProvider,
        PictureDataPersisterInterface $pictureDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        MessageBusInterface $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->albumDataProvider = $albumDataProvider;
        $this->pictureDataPersister = $pictureDataPersister;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(UploadImageCommand $command)
    {
        if (!in_array($command->getFile()->getMimeType(), MimeTypeEnum::toArray())) {
            throw new \InvalidArgumentException('The mime type is not an image accepted.');
        }
        if ($command->getFile()->getRealPath() === false) {
            throw new \InvalidArgumentException('The file uploaded does not exists.');
        }

        $album = $this->albumDataProvider->getAlbumById($command->getAlbumId());
        if ($album === null) {
            throw new NotFoundException('Album with id "' . $command->getAlbumId() . '" was not found.');
        }
        $fileName = $this->generateFileName(
            $command->getFile()->getClientOriginalName() ?? uniqid(),
            $command->getFile()->guessClientExtension() ?? AlbumPicture::FORMAT
        );

        $this->physicalDataPersister->add(
            $command->getFile()->getRealPath(),
            $album->getRelativePath() . '/' . $fileName
        );

        $size = getimagesize($command->getFile()->getRealPath());
        if (!is_array($size)) {
            throw new \RuntimeException('Cannot get image size.');
        }
        $pictureId = $this->pictureDataPersister->createPicture(
            $command->getAlbumId(),
            $fileName,
            $size[0] . 'x' . $size[1]
        );

        foreach (AlbumPicture::FILTERS as $filterType) {
            /** @var Image $image */
            $image = $this->handleCommand(new ResizeImageCommand(
                $command->getFile()->getPath(),
                $command->getFile()->getFilename(),
                $filterType,
                AlbumPicture::FORMAT,
                $command->getUserId(),
                $command->getOccurredOn()
            ));
            if ($image !== null) {
                $this->physicalDataPersister->add(
                    $image->getPath(),
                    $album->getRelativePath() . '/' . FilterTypeEnum::getFilterName($filterType) . '/' . $fileName
                );
                unlink($image->getPath());
            }
        }

        $this->eventDispatcher->dispatch(new AlbumImageUploadedEvent(
            $command->getAlbumId(),
            $pictureId,
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }

    public function generateFileName(string $fileName, string $extension): string
    {
        return hash('sha1', $fileName) . '.' . $extension;
    }
}
