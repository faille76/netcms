<?php

declare(strict_types=1);

namespace App\Contact\Application\Command\Handler;

use App\Contact\Application\Command\CreateContactCommand;
use App\Contact\Application\Event\ContactCreatedEvent;
use App\Contact\Domain\Contact;
use App\Contact\Domain\Persister\ContactDataPersisterInterface;
use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateContactCommandHandler implements CommandHandlerInterface
{
    private ContactDataProviderInterface $contactDataProvider;
    private ContactDataPersisterInterface $contactDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ContactDataProviderInterface $contactDataProvider,
        ContactDataPersisterInterface $contactDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contactDataProvider = $contactDataProvider;
        $this->contactDataPersister = $contactDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateContactCommand $command): ?Contact
    {
        $contactId = $this->contactDataPersister->createContact(
            $command->getLastName(),
            $command->getFirstName(),
            $command->getEmail(),
            $command->getRole()
        );

        $contact = $this->contactDataProvider->getContact($contactId);
        if ($contact !== null) {
            $this->eventDispatcher->dispatch(new ContactCreatedEvent(
                $contactId,
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $contact;
    }
}
