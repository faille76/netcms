<?php

declare(strict_types=1);

namespace App\Partner\Application\Command\Handler;

use App\Partner\Application\Command\CreatePartnerCommand;
use App\Partner\Application\Command\UploadPartnerImageCommand;
use App\Partner\Application\Event\PartnerCreatedEvent;
use App\Partner\Domain\Partner;
use App\Partner\Domain\Persister\PartnerDataPersisterInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreatePartnerCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private PartnerDataProviderInterface $partnerDataProvider;
    private PartnerDataPersisterInterface $partnerDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private SlugifyInterface $slugify;

    public function __construct(
        PartnerDataProviderInterface $partnerDataProvider,
        PartnerDataPersisterInterface $partnerDataPersister,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $commandBus,
        SlugifyInterface $slugify
    ) {
        $this->partnerDataProvider = $partnerDataProvider;
        $this->partnerDataPersister = $partnerDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->commandBus = $commandBus;
        $this->slugify = $slugify;
    }

    public function __invoke(CreatePartnerCommand $command): ?Partner
    {
        $slug = $this->slugify->slugify($command->getName()) . '-' . uniqid();

        $image = $this->handleCommand(new UploadPartnerImageCommand(
            $command->getImage(),
            $slug,
            $command->getUserId(),
            $command->getOccurredOn()
        ));

        $partnerId = $this->partnerDataPersister->createPartner(
            $command->getName(),
            $command->getUrl(),
            $image,
            $command->isEnabled()
        );
        $partner = $this->partnerDataProvider->getPartnerById($partnerId);
        if ($partner !== null) {
            $this->eventDispatcher->dispatch(new PartnerCreatedEvent(
                $partnerId,
                $command->getName(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }

        return $partner;
    }
}
