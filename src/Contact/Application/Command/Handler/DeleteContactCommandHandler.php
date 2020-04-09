<?php

declare(strict_types=1);

namespace App\Contact\Application\Command\Handler;

use App\Contact\Application\Command\DeleteContactCommand;
use App\Contact\Application\Event\ContactDeletedEvent;
use App\Contact\Domain\Persister\ContactDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteContactCommandHandler implements CommandHandlerInterface
{
    private ContactDataPersisterInterface $contactDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ContactDataPersisterInterface $contactDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contactDataPersister = $contactDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(DeleteContactCommand $command): void
    {
        $this->contactDataPersister->deleteContact($command->getContactId());

        $this->eventDispatcher->dispatch(new ContactDeletedEvent(
            $command->getContactId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
