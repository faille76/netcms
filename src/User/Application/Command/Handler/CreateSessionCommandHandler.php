<?php

declare(strict_types=1);

namespace App\User\Application\Command\Handler;

use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\User\Application\Command\CreateSessionCommand;
use App\User\Application\Event\SessionCreatedEvent;
use App\User\Domain\Persister\SessionDataPersisterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateSessionCommandHandler implements CommandHandlerInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private SessionDataPersisterInterface $sessionDataPersister;

    public function __construct(
        SessionDataPersisterInterface $sessionDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->sessionDataPersister = $sessionDataPersister;
    }

    public function __invoke(CreateSessionCommand $command): string
    {
        $token = md5(uniqid());
        $this->sessionDataPersister->createSession(
            $command->getUserId(),
            $token,
            $command->getIp(),
            $command->getUserAgent()
        );

        $this->eventDispatcher->dispatch(new SessionCreatedEvent(
            $command->getIp(),
            $command->getUserAgent(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));

        return $token;
    }
}
