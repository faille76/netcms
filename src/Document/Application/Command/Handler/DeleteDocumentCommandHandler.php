<?php

declare(strict_types=1);

namespace App\Document\Application\Command\Handler;

use App\Document\Application\Command\DeleteDocumentCommand;
use App\Document\Application\Event\DocumentDeletedEvent;
use App\Document\Domain\Persister\DocumentDataPersisterInterface;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteDocumentCommandHandler implements CommandHandlerInterface
{
    private DocumentDataPersisterInterface $documentDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private DocumentDataProviderInterface $documentDataProvider;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        DocumentDataProviderInterface $documentDataProvider,
        DocumentDataPersisterInterface $documentDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->documentDataPersister = $documentDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->documentDataProvider = $documentDataProvider;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(DeleteDocumentCommand $command): void
    {
        $document = $this->documentDataProvider->getDocument($command->getDocumentId());
        if ($document === null) {
            throw new NotFoundException('Document id "' . $command->getDocumentId() . '" was not found.');
        }

        $this->physicalDataPersister->remove('upload/' . $document->getFileName());

        $this->documentDataPersister->deleteDocument($command->getDocumentId());

        $this->eventDispatcher->dispatch(new DocumentDeletedEvent(
            $command->getDocumentId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
