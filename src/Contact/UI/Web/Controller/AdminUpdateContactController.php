<?php

declare(strict_types=1);

namespace App\Contact\UI\Web\Controller;

use App\Contact\Application\Command\UpdateContactCommand;
use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdateContactController extends AbstractController
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
     * @Route("/admin/contacts/{contactId}/update", name="admin_contact_update_post", methods={"POST"})
     * @IsGranted("ROLE_CONTACT")
     */
    public function post(Request $request, int $contactId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CONTACT)) {
            throw $this->createNotFoundException();
        }

        $contact = $this->contactDataProvider->getContact($contactId);
        if ($contact === null) {
            throw $this->createNotFoundException();
        }

        $contactForm = [
            'first_name' => $request->request->get('first_name', null),
            'last_name' => $request->request->get('last_name', null),
            'role' => $request->request->get('role', null),
            'email' => $request->request->get('email', null),
        ];

        $render = [];

        try {
            $this->validate($contactForm);
            $this->commandBus->dispatch(
                new UpdateContactCommand(
                    $contact->getId(),
                    $contactForm['last_name'],
                    $contactForm['first_name'],
                    $contactForm['email'],
                    $contactForm['role'],
                    $this->getUserId(),
                    time()
                )
            );
            $render['succeed'] = 'admin.contacts.create.confirm.contact_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/contact_create.html.twig', array_merge([
            'first_name' => $contactForm['first_name'],
            'last_name' => $contactForm['last_name'],
            'role' => $contactForm['role'],
            'email' => $contactForm['email'],
        ], $render));
    }

    /**
     * @Route("/admin/contacts/{contactId}/update", name="admin_contact_update", methods={"GET"})
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

        return $this->render('admin/contact_create.html.twig', [
            'last_name' => $contact->getLastName(),
            'first_name' => $contact->getFirstName(),
            'role' => $contact->getRole(),
            'email' => $contact->getEmail(),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $contact): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($contact['last_name'])
            ->notEmpty('admin.contacts.create.error.last_name_empty')
            ->that($contact['first_name'])
            ->notEmpty('admin.contacts.create.error.first_name_empty')
            ->that($contact['role'])
            ->notEmpty('admin.contacts.create.error.role_empty')
            ->that($contact['email'])
            ->notEmpty('admin.contacts.create.error.email_empty')
            ->email('admin.contacts.create.error.email_invalid')
        ;
        $lazyAssert->verifyNow();
    }
}
