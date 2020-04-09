<?php

declare(strict_types=1);

namespace App\Document\Application\Command\Handler;

use App\Document\Application\Command\CreateDocumentCommand;
use App\Document\Application\Event\DocumentCreatedEvent;
use App\Document\Domain\Document;
use App\Document\Domain\Persister\DocumentDataPersisterInterface;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateDocumentCommandHandler implements CommandHandlerInterface
{
    private DocumentDataProviderInterface $documentDataProvider;
    private DocumentDataPersisterInterface $documentDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private SlugifyInterface $slugify;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        DocumentDataProviderInterface $documentDataProvider,
        DocumentDataPersisterInterface $documentDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        EventDispatcherInterface $eventDispatcher,
        SlugifyInterface $slugify
    ) {
        $this->documentDataProvider = $documentDataProvider;
        $this->documentDataPersister = $documentDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->slugify = $slugify;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(CreateDocumentCommand $command): ?Document
    {
        $fileName = $this->slugify->slugify($command->getName()) . '-' . uniqid() . '.' . $command->getFile()->getClientOriginalExtension();

        $fromPath = $command->getFile()->getRealPath();
        if ($fromPath === false) {
            throw new \InvalidArgumentException('File path should be a string.');
        }

        $this->physicalDataPersister->add($fromPath, 'upload/' . $fileName);

        $documentId = $this->documentDataPersister->createDocument(
            $command->getName(),
            $fileName,
            $command->getUserId(),
            $command->getFile()->getClientOriginalExtension(),
            $command->isEnabled()
        );

        $document = $this->documentDataProvider->getDocument($documentId);
        if ($document !== null) {
            $this->eventDispatcher->dispatch(new DocumentCreatedEvent(
                $documentId,
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $document;
    }
}
