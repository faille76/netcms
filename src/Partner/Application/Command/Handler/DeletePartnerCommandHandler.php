<?php

declare(strict_types=1);

namespace App\Partner\Application\Command\Handler;

use App\ImageFactory\Domain\FilterTypeEnum;
use App\Partner\Application\Command\DeletePartnerCommand;
use App\Partner\Application\Event\PartnerDeletedEvent;
use App\Partner\Domain\PartnerImage;
use App\Partner\Domain\Persister\PartnerDataPersisterInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeletePartnerCommandHandler implements CommandHandlerInterface
{
    private PartnerDataPersisterInterface $partnerDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PartnerDataProviderInterface $partnerDataProvider;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        PartnerDataPersisterInterface $partnerDataPersister,
        PartnerDataProviderInterface $partnerDataProvider,
        PhysicalDataPersisterInterface $physicalDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->partnerDataPersister = $partnerDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(DeletePartnerCommand $command): void
    {
        $partner = $this->partnerDataProvider->getPartnerById($command->getPartnerId());
        if ($partner === null) {
            throw new NotFoundException('Partner id ' . $command->getPartnerId() . ' is not found.');
        }

        $this->physicalDataPersister->remove(PartnerImage::PATH_BASE . $partner->getImage());
        foreach (PartnerImage::FILTERS as $filterType) {
            $this->physicalDataPersister->remove(
                PartnerImage::PATH_BASE . FilterTypeEnum::getFilterName($filterType) . '/' . $partner->getImage()
            );
        }

        $this->partnerDataPersister->deletePartner(
            $command->getPartnerId()
        );

        $this->eventDispatcher->dispatch(new PartnerDeletedEvent(
            $command->getPartnerId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
