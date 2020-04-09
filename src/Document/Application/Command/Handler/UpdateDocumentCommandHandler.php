<?php

declare(strict_types=1);

namespace App\Document\Application\Command\Handler;

use App\Document\Application\Command\UpdateDocumentCommand;
use App\Document\Application\Event\DocumentUpdatedEvent;
use App\Document\Domain\Persister\DocumentDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateDocumentCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateDocumentCommand $command): void
    {
        $this->documentDataPersister->updateDocument($command->getDocumentId(), $command->getName());

        $this->eventDispatcher->dispatch(new DocumentUpdatedEvent(
            $command->getDocumentId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
