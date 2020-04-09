<?php

declare(strict_types=1);

namespace App\User\Application\Command\Handler;

use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\User\Application\Command\UpdateUserPasswordCommand;
use App\User\Application\Event\UserPasswordUpdatedEvent;
use App\User\Domain\Persister\UserDataPersisterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateUserPasswordCommandHandler implements CommandHandlerInterface
{
    private UserDataPersisterInterface $userDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserDataPersisterInterface $userDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userDataPersister = $userDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateUserPasswordCommand $command): void
    {
        $this->userDataPersister->updateUserPassword(
            $command->getUserId(),
            $command->getPassword()
        );

        $this->eventDispatcher->dispatch(new UserPasswordUpdatedEvent(
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
