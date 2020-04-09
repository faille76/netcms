<?php

declare(strict_types=1);

namespace App\User\Application\Command\Handler;

use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Event\UserCreatedEvent;
use App\User\Domain\Persister\UserDataPersisterInterface;
use App\User\Domain\Provider\UserDataProviderInterface;
use App\User\Domain\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateUserCommandHandler implements CommandHandlerInterface
{
    private UserDataPersisterInterface $userDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        UserDataProviderInterface $userDataProvider,
        UserDataPersisterInterface $userDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userDataPersister = $userDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->userDataProvider = $userDataProvider;
    }

    public function __invoke(CreateUserCommand $command): ?User
    {
        $userId = $this->userDataPersister->createUser(
            $command->getFirstName(),
            $command->getLastName(),
            $command->getUsername(),
            md5(md5($command->getPassword())),
            $command->getEmail(),
            $command->getGender(),
            $command->getBirthday(),
            $command->isNewsletterEnabled()
        );

        $user = $this->userDataProvider->getUserById($userId);
        if ($user !== null) {
            $this->eventDispatcher->dispatch(new UserCreatedEvent(
                $userId,
                $command->getOccurredOn()
            ));
        }

        return $user;
    }
}
