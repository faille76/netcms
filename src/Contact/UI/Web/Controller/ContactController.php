<?php

declare(strict_types=1);

namespace App\Contact\UI\Web\Controller;

use App\Contact\Application\Command\SendEmailCommand;
use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\Domain\Criteria;
use App\Shared\UI\Web\Controller\Service\ReCaptchaValidator;
use Assert\Assert;
use Assert\LazyAssertionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ContactController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private ContactDataProviderInterface $contactDataProvider;
    private MessageBusInterface $commandBus;
    private ReCaptchaValidator $reCaptchaValidator;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ContactDataProviderInterface $contactDataProvider,
        ReCaptchaValidator $reCaptchaValidator,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->contactDataProvider = $contactDataProvider;
        $this->commandBus = $commandBus;
        $this->reCaptchaValidator = $reCaptchaValidator;
    }

    /**
     * @Route("/contact", name="contact_post", methods={"POST"})
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CONTACT)) {
            throw $this->createNotFoundException();
        }

        $contactForm = [
            'contact_id' => $request->request->getInt('contact_id', 0),
            'last_name' => $request->request->get('last_name', null),
            'first_name' => $request->request->get('first_name', null),
            'subject' => $request->request->get('subject', null),
            'email' => $request->request->get('email', null),
            'message' => $request->request->get('message', null),
        ];

        $render = [];

        try {
            $this->validate($request, $contactForm);

            $contact = $this->contactDataProvider->getContact($contactForm['contact_id']);
            if ($contact === null) {
                throw new \InvalidArgumentException('contact.post.error.contact_not_found');
            }

            $message = $this->renderView('mail/contact.html.twig', [
                'last_name' => $contactForm['last_name'],
                'first_name' => $contactForm['first_name'],
                'message' => $contactForm['message'],
                'email' => $contactForm['email'],
                'subject' => $contactForm['subject'],
                'contact' => $contact,
                'createdAt' => time(),
            ]);

            $this->commandBus->dispatch(new SendEmailCommand(
                $contactForm['email'],
                $contactForm['subject'],
                $contact->getEmail(),
                $message,
                0,
                time()
            ));
            $render['succeed'] = 'contact.post.confirm.message_sent';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('contact/index.html.twig', array_merge([
            'contacts' => $this->contactDataProvider->findContacts(new Criteria(['id' => 'ASC'])),
            'last_name' => $contactForm['last_name'],
            'first_name' => $contactForm['first_name'],
            'message' => $contactForm['message'],
            'email' => $contactForm['email'],
            'subject' => $contactForm['subject'],
            'contact_id' => $contactForm['contact_id'],
        ], $render));
    }

    /**
     * @Route("/contact", name="contact", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CONTACT)) {
            throw $this->createNotFoundException();
        }

        return $this->render('contact/index.html.twig', [
            'contacts' => $this->contactDataProvider->findContacts(new Criteria(['id' => 'ASC'])),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(Request $request, array $email): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert->that($this->reCaptchaValidator->verify($request))->true('contact.post.error.captcha_invalid');
        $lazyAssert
            ->that($email['last_name'])
            ->notEmpty('contact.post.error.last_name_empty')
            ->betweenLength(1, 25, 'contact.post.error.last_name_invalid')
            ->that($email['first_name'])
            ->notEmpty('contact.post.error.first_name_empty')
            ->betweenLength(1, 25, 'contact.post.error.first_name_invalid')
            ->that($email['email'])
            ->notEmpty('contact.post.error.email_empty')
            ->email('contact.post.error.email_invalid')
            ->that($email['subject'])
            ->notEmpty('contact.post.error.subject_empty')
            ->betweenLength(4, 30, 'contact.post.error.subject_invalid')
            ->that($email['message'])
            ->notEmpty('contact.post.error.message_empty')
            ->betweenLength(20, 20000, 'contact.post.error.message_invalid')
        ;
        $lazyAssert->verifyNow();
    }
}
