<?php

declare(strict_types=1);

namespace App\Partner\Application\Command\Handler;

use App\Partner\Application\Command\UpdatePartnerCommand;
use App\Partner\Application\Command\UploadPartnerImageCommand;
use App\Partner\Application\Event\PartnerUpdatedEvent;
use App\Partner\Domain\Persister\PartnerDataPersisterInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdatePartnerCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private PartnerDataPersisterInterface $partnerDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PartnerDataProviderInterface $partnerDataProvider;
    private MessageBusInterface $commandBus;
    private SlugifyInterface $slugify;

    public function __construct(
        PartnerDataProviderInterface $partnerDataProvider,
        PartnerDataPersisterInterface $partnerDataPersister,
        MessageBusInterface $commandBus,
        SlugifyInterface $slugify,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->partnerDataPersister = $partnerDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->slugify = $slugify;
        $this->commandBus = $commandBus;
    }

    public function __invoke(UpdatePartnerCommand $command): void
    {
        $slug = $this->slugify->slugify($command->getName()) . '-' . uniqid();

        $partner = $this->partnerDataProvider->getPartnerById($command->getPartnerId());
        if ($partner === null) {
            throw new NotFoundException('Partner ' . $command->getPartnerId() . ' does not exists.');
        }
        $image = $command->getImage() !== null ? $this->handleCommand(new UploadPartnerImageCommand(
            $command->getImage(),
            $slug,
            $command->getUserId(),
            $command->getOccurredOn()
        )) : $partner->getImage();

        $this->partnerDataPersister->updatePartner(
            $command->getPartnerId(),
            $command->getName(),
            $command->getUrl(),
            $image
        );

        $this->eventDispatcher->dispatch(new PartnerUpdatedEvent(
            $command->getPartnerId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
