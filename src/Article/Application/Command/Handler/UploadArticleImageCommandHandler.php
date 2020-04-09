<?php

declare(strict_types=1);

namespace App\Article\Application\Command\Handler;

use App\Article\Application\Command\UploadArticleImageCommand;
use App\Article\Domain\ArticleImage;
use App\ImageFactory\Application\Command\ResizeImageCommand;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\ImageFactory\Domain\Image;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class UploadArticleImageCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UploadArticleImageCommand $command): string
    {
        if (!in_array($command->getFile()->getMimeType(), MimeTypeEnum::toArray())) {
            throw new \InvalidArgumentException('The mime type "' . $command->getFile()->getMimeType() . '" is not accepted.');
        }
        if ($command->getFile()->getRealPath() === false) {
            throw new \InvalidArgumentException('File uploaded cannot be loaded.');
        }

        $imageName = $command->getSlug() . '.' . ArticleImage::FORMAT;

        $this->physicalDataPersister->add(
            $command->getFile()->getRealPath(),
            ArticleImage::PATH_BASE . $imageName
        );

        foreach (ArticleImage::FILTERS as $filterType) {
            /** @var Image $image */
            $image = $this->handleCommand(new ResizeImageCommand(
                $command->getFile()->getPath(),
                $command->getFile()->getFilename(),
                $filterType,
                ArticleImage::FORMAT,
                $command->getUserId(),
                $command->getOccurredOn()
            ));
            if ($image !== null) {
                $this->physicalDataPersister->add(
                    $image->getPath(),
                    ArticleImage::PATH_BASE . FilterTypeEnum::getFilterName($filterType) . '/' . $imageName
                );
                unlink($image->getPath());
            }
        }

        return $imageName;
    }
}
