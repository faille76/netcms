<?php

declare(strict_types=1);

namespace App\Gallery\Application\Command\Handler;

use App\Gallery\Application\Command\UpdateCategoryCommand;
use App\Gallery\Application\Event\CategoryUpdatedEvent;
use App\Gallery\Domain\Persister\CategoryDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateCategoryCommandHandler implements CommandHandlerInterface
{
    private CategoryDataPersisterInterface $categoryDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CategoryDataPersisterInterface $categoryDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->categoryDataPersister = $categoryDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateCategoryCommand $command): void
    {
        $this->categoryDataPersister->updateCategory($command->getCategoryId(), $command->getName(), $command->getParentId());
        $this->eventDispatcher->dispatch(new CategoryUpdatedEvent(
            $command->getCategoryId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
