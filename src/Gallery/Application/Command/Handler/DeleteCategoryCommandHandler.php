<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\DeleteCategoryCommand;
use App\Gallery\Application\Command\UpdateAlbumCommand;
use App\Gallery\Application\Command\UpdateCategoryCommand;
use App\Gallery\Application\Event\CategoryDeletedEvent;
use App\Gallery\Domain\Album;
use App\Gallery\Domain\Category;
use App\Gallery\Domain\Persister\AlbumDataPersisterInterface;
use App\Gallery\Domain\Persister\CategoryDataPersisterInterface;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteCategoryCommandHandler implements CommandHandlerInterface
{
    private CategoryDataPersisterInterface $categoryDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private CategoryDataProviderInterface $categoryDataProvider;
    private AlbumDataProviderInterface $albumDataProvider;
    private AlbumDataPersisterInterface $albumDataPersister;
    private MessageBusInterface $commandBus;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider,
        CategoryDataPersisterInterface $categoryDataPersister,
        AlbumDataProviderInterface $albumDataProvider,
        AlbumDataPersisterInterface $albumDataPersister,
        MessageBusInterface $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->categoryDataPersister = $categoryDataPersister;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->albumDataProvider = $albumDataProvider;
        $this->albumDataPersister = $albumDataPersister;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $category = $this->categoryDataProvider->getCategoryById($command->getCategoryId());
        if ($category === null) {
            throw new NotFoundException('Category id "' . $command->getCategoryId() . '" does not exists.');
        }

        $subCategories = $this->categoryDataProvider->findCategoriesByParentId($command->getCategoryId());
        /** @var Category $subCategory */
        foreach ($subCategories as $subCategory) {
            $this->commandBus->dispatch(new UpdateCategoryCommand(
                $subCategory->getId(),
                $subCategory->getName(),
                $category->getParentId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        $albums = $this->albumDataProvider->findAlbumsFromCategoryId($command->getCategoryId());
        /** @var Album $album */
        foreach ($albums as $album) {
            $this->commandBus->dispatch(new UpdateAlbumCommand(
                $album->getId(),
                $album->getName(),
                $category->getParentId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        $this->categoryDataPersister->deleteCategory($command->getCategoryId());

        $this->eventDispatcher->dispatch(new CategoryDeletedEvent(
            $command->getCategoryId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
