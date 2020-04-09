<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\UpdateImgPageCommand;
use App\Page\Application\Event\PageContentUpdatedEvent;
use App\Page\Domain\Persister\ImagePageDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateImgPageCommandHandler implements CommandHandlerInterface
{
    private ImagePageDataPersisterInterface $imagePageDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ImagePageDataPersisterInterface $imagePageDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->imagePageDataPersister = $imagePageDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateImgPageCommand $command): void
    {
        $this->imagePageDataPersister->updateImgPage(
            $command->getId(),
            $command->getName()
        );
        $this->eventDispatcher->dispatch(new PageContentUpdatedEvent(
            $command->getPageId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
