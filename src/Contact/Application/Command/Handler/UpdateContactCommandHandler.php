<?php

declare(strict_types=1);

namespace App\Contact\Application\Command\Handler;

use App\Contact\Application\Command\UpdateContactCommand;
use App\Contact\Application\Event\ContactUpdatedEvent;
use App\Contact\Domain\Persister\ContactDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateContactCommandHandler implements CommandHandlerInterface
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

    public function __invoke(UpdateContactCommand $command): void
    {
        $this->contactDataPersister->updateContact(
            $command->getContactId(),
            $command->getLastName(),
            $command->getFirstName(),
            $command->getEmail(),
            $command->getRole()
        );

        $this->eventDispatcher->dispatch(new ContactUpdatedEvent(
            $command->getContactId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
