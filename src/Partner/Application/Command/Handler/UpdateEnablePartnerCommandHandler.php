<?php

declare(strict_types=1);

namespace App\Partner\Application\Command\Handler;

use App\Partner\Application\Command\UpdateEnablePartnerCommand;
use App\Partner\Application\Event\PartnerUpdatedEvent;
use App\Partner\Domain\Persister\PartnerDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateEnablePartnerCommandHandler implements CommandHandlerInterface
{
    private PartnerDataPersisterInterface $partnerDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PartnerDataPersisterInterface $partnerDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->partnerDataPersister = $partnerDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateEnablePartnerCommand $command): void
    {
        $this->partnerDataPersister->updatePartnerEnabled(
            $command->getPartnerId(),
            $command->isEnabled()
        );

        $this->eventDispatcher->dispatch(new PartnerUpdatedEvent(
            $command->getPartnerId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
