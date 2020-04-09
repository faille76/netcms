<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\ImageFactory\Application\Command\ResizeImageCommand;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\ImageFactory\Domain\Image;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Page\Application\Command\UploadPageImageCommand;
use App\Page\Domain\PageImage;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class UploadPageImageCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        PhysicalDataPersisterInterface $physicalDataPersister,
        MessageBusInterface $commandBus
    ) {
        $this->commandBus = $commandBus;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(UploadPageImageCommand $command): string
    {
        if (!in_array($command->getFile()->getMimeType(), MimeTypeEnum::toArray())) {
            throw new \InvalidArgumentException('The mime type "' . $command->getFile()->getMimeType() . '" is not accepted.');
        }
        if ($command->getFile()->getRealPath() === false) {
            throw new \InvalidArgumentException('File uploaded cannot be loaded.');
        }

        $imageName = $command->getSlug() . '.' . PageImage::FORMAT;

        $this->physicalDataPersister->add(
            $command->getFile()->getRealPath(),
            PageImage::PATH_BASE . '/' . $command->getPageSlug() . '/' . $imageName
        );

        foreach (PageImage::FILTERS as $filterType) {
            /** @var Image $image */
            $image = $this->handleCommand(new ResizeImageCommand(
                $command->getFile()->getPath(),
                $command->getFile()->getFilename(),
                $filterType,
                PageImage::FORMAT,
                $command->getUserId(),
                $command->getOccurredOn()
            ));
            if ($image !== null) {
                $this->physicalDataPersister->add(
                    $image->getPath(),
                    PageImage::PATH_BASE . '/' . $command->getPageSlug() . '/' . FilterTypeEnum::getFilterName($filterType) . '/' . $imageName
                );
                unlink($image->getPath());
            }
        }

        return $imageName;
    }
}
