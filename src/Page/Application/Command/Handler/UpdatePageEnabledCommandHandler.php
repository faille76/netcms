<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\UpdatePageEnabledCommand;
use App\Page\Application\Event\PageUpdatedEvent;
use App\Page\Domain\Persister\PageDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdatePageEnabledCommandHandler implements CommandHandlerInterface
{
    private PageDataPersisterInterface $pageDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PageDataPersisterInterface $pageDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->pageDataPersister = $pageDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdatePageEnabledCommand $command): void
    {
        $this->pageDataPersister->updatePageEnabled($command->getPageId(), $command->isEnabled());
        $this->eventDispatcher->dispatch(new PageUpdatedEvent(
            $command->getPageId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
