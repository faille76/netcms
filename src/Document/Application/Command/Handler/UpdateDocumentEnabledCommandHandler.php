<?php

declare(strict_types=1);

namespace App\Document\Application\Command\Handler;

use App\Document\Application\Command\UpdateDocumentEnabledCommand;
use App\Document\Application\Event\DocumentUpdatedEvent;
use App\Document\Domain\Persister\DocumentDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateDocumentEnabledCommandHandler implements CommandHandlerInterface
{
    private DocumentDataPersisterInterface $documentDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        DocumentDataPersisterInterface $documentDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->documentDataPersister = $documentDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateDocumentEnabledCommand $command): void
    {
        $this->documentDataPersister->updateDocumentEnabled($command->getDocumentId(), $command->isEnabled());

        $this->eventDispatcher->dispatch(new DocumentUpdatedEvent(
            $command->getDocumentId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
