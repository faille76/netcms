<?php

declare(strict_types=1);

namespace App\Contact\UI\Web\Controller;

use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\Domain\Criteria;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListContactController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private ContactDataProviderInterface $contactDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ContactDataProviderInterface $contactDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->contactDataProvider = $contactDataProvider;
    }

    /**
     * @Route("/admin/contacts", name="admin_contact_list", methods={"GET"})
     * @IsGranted("ROLE_CONTACT")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CONTACT)) {
            throw $this->createNotFoundException();
        }

        $contacts = $this->contactDataProvider->findContacts(new Criteria(['id' => 'DESC']));

        return $this->render('admin/contact_list.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}
