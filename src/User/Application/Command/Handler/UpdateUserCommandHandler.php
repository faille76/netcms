<?php

declare(strict_types=1);

namespace App\User\Application\Command\Handler;

use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Command\UploadUserAvatarCommand;
use App\User\Application\Event\UserUpdatedEvent;
use App\User\Domain\Persister\UserDataPersisterInterface;
use App\User\Domain\Provider\UserDataProviderInterface;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateUserCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private UserDataPersisterInterface $userDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private UserDataProviderInterface $userDataProvider;
    private SlugifyInterface $slugify;

    public function __construct(
        UserDataProviderInterface $userDataProvider,
        UserDataPersisterInterface $userDataPersister,
        MessageBusInterface $commandBus,
        SlugifyInterface $slugify,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userDataPersister = $userDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->userDataProvider = $userDataProvider;
        $this->commandBus = $commandBus;
        $this->slugify = $slugify;
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->userDataProvider->getUserById($command->getUserId());
        if ($user === null) {
            throw new NotFoundException('User ' . $command->getUserId() . ' does not exists.');
        }
        $avatarSlug = $this->slugify->slugify($user->getFirstName() . ' ' . $user->getLastName() . ' ' . $user->getId());

        $image = $command->getAvatar() !== null ? $this->handleCommand(new UploadUserAvatarCommand(
            $command->getAvatar(),
            $avatarSlug,
            $command->getUserId(),
            $command->getOccurredOn()
        )) : $user->getAvatar();

        $this->userDataPersister->updateUser(
            $command->getUserId(),
            $command->getEmail(),
            $image,
            $command->isNewsletter()
        );

        $this->eventDispatcher->dispatch(new UserUpdatedEvent(
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
