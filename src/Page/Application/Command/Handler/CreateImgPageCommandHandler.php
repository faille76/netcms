<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\CreateImgPageCommand;
use App\Page\Application\Command\UploadPageImageCommand;
use App\Page\Application\Event\PageContentUpdatedEvent;
use App\Page\Domain\Persister\ImagePageDataPersisterInterface;
use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Image;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateImgPageCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private ImagePageDataProviderInterface $imagePageDataProvider;
    private ImagePageDataPersisterInterface $imagePageDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PageDataProviderInterface $pageDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        ImagePageDataProviderInterface $imagePageDataProvider,
        ImagePageDataPersisterInterface $imagePageDataPersister,
        MessageBusInterface $commandBus,
        EventDispatcherInterface $eventDispatcher,
        SlugifyInterface $slugify
    ) {
        $this->imagePageDataProvider = $imagePageDataProvider;
        $this->imagePageDataPersister = $imagePageDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageDataProvider = $pageDataProvider;
        $this->commandBus = $commandBus;
        $this->slugify = $slugify;
    }

    public function __invoke(CreateImgPageCommand $command): ?Image
    {
        $page = $this->pageDataProvider->getPageById($command->getPageId(), 'fr');
        if ($page === null) {
            throw new NotFoundException('Page id ' . $command->getPageId() . ' was not found.');
        }
        if ($command->getImage()->getRealPath() === false) {
            throw new \InvalidArgumentException('File uploaded is invalid.');
        }

        $slug = $this->slugify->slugify($command->getName()) . '-' . uniqid();

        $size = getimagesize($command->getImage()->getRealPath());
        if (is_array($size)) {
            $size = "{$size[0]}x{$size[1]}";
        } else {
            $size = 'null';
        }

        $image = $this->handleCommand(new UploadPageImageCommand(
            $command->getImage(),
            $page->getSlug(),
            $slug,
            $command->getUserId(),
            $command->getOccurredOn()
        ));

        $imgPage = $this->imagePageDataPersister->createImgPage(
            $command->getPageId(),
            $command->getName(),
            $image,
            $size
        );
        $img = $this->imagePageDataProvider->getImageById($imgPage);
        if ($img !== null) {
            $this->eventDispatcher->dispatch(new PageContentUpdatedEvent(
                $command->getPageId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $img;
    }
}
