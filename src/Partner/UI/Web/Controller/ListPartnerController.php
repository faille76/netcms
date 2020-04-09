<?php

declare(strict_types=1);

namespace App\Partner\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListPartnerController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private PartnerDataProviderInterface $partnerDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        PartnerDataProviderInterface $partnerDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->partnerDataProvider = $partnerDataProvider;
    }

    /**
     * @Route("/partner", name="partners", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
            throw $this->createNotFoundException();
        }

        return $this->render('partner/index.html.twig', [
            'partners' => $this->partnerDataProvider->findPartners(true),
        ]);
    }
}
