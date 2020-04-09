<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\CreatePageCommand;
use App\Page\Application\Event\PageCreatedEvent;
use App\Page\Application\Query\GeneratePageSlugQuery;
use App\Page\Domain\Persister\PageDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreatePageCommandHandler implements CommandHandlerInterface
{
    use QueryHandleTrait;

    private PageDataPersisterInterface $pageDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PageDataPersisterInterface $pageDataPersister,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $queryBus
    ) {
        $this->pageDataPersister = $pageDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->queryBus = $queryBus;
    }

    public function __invoke(CreatePageCommand $command): int
    {
        $slug = $this->handleQuery(new GeneratePageSlugQuery($command->getName()));

        $pageId = $this->pageDataPersister->createPage(
            $slug,
            $command->getParentId(),
            $command->isEnabled()
        );
        if ($pageId !== 0) {
            $this->eventDispatcher->dispatch(new PageCreatedEvent(
                $pageId,
                $slug,
                $command->getParentId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $pageId;
    }
}
