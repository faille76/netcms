<?php

declare(strict_types=1);

namespace App\Partner\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Partner\Application\Command\DeletePartnerCommand;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeletePartnerController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private PartnerDataProviderInterface $partnerDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        PartnerDataProviderInterface $partnerDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/partners/{partnerId}/delete", name="admin_partner_delete", methods={"GET"})
     * @IsGranted("ROLE_PARTNERS")
     */
    public function __invoke(Request $request, int $partnerId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
            throw $this->createNotFoundException();
        }

        $partner = $this->partnerDataProvider->getPartnerById($partnerId);
        if ($partner === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new DeletePartnerCommand(
            $partner->getId(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_partners_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
