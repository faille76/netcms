<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\CreateCategoryCommand;
use App\Gallery\Application\Event\CategoryCreatedEvent;
use App\Gallery\Application\Query\GenerateCategorySlugQuery;
use App\Gallery\Application\Query\GenerateRelativePathQuery;
use App\Gallery\Domain\Category;
use App\Gallery\Domain\Persister\CategoryDataPersisterInterface;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateCategoryCommandHandler implements CommandHandlerInterface
{
    use QueryHandleTrait;

    private CategoryDataProviderInterface $categoryDataProvider;
    private CategoryDataPersisterInterface $categoryDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider,
        CategoryDataPersisterInterface $categoryDataPersister,
        MessageBusInterface $queryBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->categoryDataPersister = $categoryDataPersister;
        $this->queryBus = $queryBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateCategoryCommand $command): ?Category
    {
        $slug = $this->handleQuery(new GenerateCategorySlugQuery($command->getName()));
        $relativePath = $this->handleQuery(new GenerateRelativePathQuery($command->getParentId(), $slug));
        $categoryId = $this->categoryDataPersister->createCategory(
            $command->getName(),
            $relativePath,
            $slug,
            $command->getParentId(),
            $command->isEnabled()
        );
        $category = $this->categoryDataProvider->getCategoryById($categoryId);
        if ($category !== null) {
            $this->eventDispatcher->dispatch(new CategoryCreatedEvent(
                $categoryId,
                $slug,
                $command->getParentId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $category;
    }
}
