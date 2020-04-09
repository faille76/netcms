<?php

declare(strict_types=1);

namespace App\Contact\UI\Web\Controller;

use App\Contact\Application\Command\DeleteContactCommand;
use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteContactController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private ContactDataProviderInterface $contactDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ContactDataProviderInterface $contactDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->contactDataProvider = $contactDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/contacts/{contactId}/delete", name="admin_contact_delete", methods={"GET"})
     * @IsGranted("ROLE_CONTACT")
     */
    public function __invoke(Request $request, int $contactId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CONTACT)) {
            throw $this->createNotFoundException();
        }

        $contact = $this->contactDataProvider->getContact($contactId);
        if ($contact === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new DeleteContactCommand(
            $contact->getId(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_contact_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
