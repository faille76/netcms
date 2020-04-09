<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\UI\Web\Controller\Service\ReCaptchaValidator;
use App\Shared\UI\Web\Controller\UserTrait;
use App\User\Application\Command\CreateSessionCommand;
use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Provider\UserDataProviderInterface;
use App\User\Domain\User;
use Assert\Assert;
use Assert\Assertion;
use Assert\LazyAssertionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegisterController extends AbstractController
{
    use CommandHandleTrait;
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private UserDataProviderInterface $userDataProvider;
    private ReCaptchaValidator $captchaValidator;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        UserDataProviderInterface $userDataProvider,
        ReCaptchaValidator $captchaValidator,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->userDataProvider = $userDataProvider;
        $this->captchaValidator = $captchaValidator;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/register", name="user_register_submit", methods={"POST"})
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::REGISTER)) {
            throw $this->createNotFoundException();
        }

        $userForm = [
            'gender' => $request->request->get('gender', null),
            'username' => $request->request->get('username', null),
            'first_name' => $request->request->get('first_name', null),
            'last_name' => $request->request->get('last_name', null),
            'email' => $request->request->get('email', null),
            'birth_day' => $request->request->getInt('birth_day', 0),
            'birth_month' => $request->request->getInt('birth_month', 0),
            'birth_year' => $request->request->getInt('birth_year', 0),
            'password' => $request->request->get('password', null),
            'password_repeat' => $request->request->get('password_repeat', null),
            'newsletter' => $request->request->get('newsletter', 'off') === 'on' ? true : false,
        ];

        try {
            $this->validate($userForm, $request);
            $user = $this->createUser($request, $userForm);
            Assertion::notNull($user, 'register.errors.internal_error');

            return $this->redirectToRoute('home.lang', [
                '_locale' => $request->getLocale(),
            ]);
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
        } catch (\InvalidArgumentException $e) {
            $errorList = [$e->getMessage()];
        }

        return $this->render('register/index.html.twig', [
            'errors' => $errorList,
            'post' => $userForm,
        ]);
    }

    /**
     * @Route("/register", name="user_register", methods={"GET"})
     */
    public function index(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::REGISTER)) {
            throw $this->createNotFoundException();
        }

        return $this->render('register/index.html.twig');
    }

    private function createUser(Request $request, array $user): ?User
    {
        /** @var User|null $user */
        $user = $this->handleCommand(new CreateUserCommand(
            $user['last_name'],
            $user['first_name'],
            $user['username'],
            $user['password'],
            $user['email'],
            ($user['gender'] == '1') ? 'h' : 'f',
            $user['birth_year'] . '-' . $user['birth_month'] . '-' . $user['birth_day'],
            (bool) $user['newsletter'],
            time()
        ));
        if ($user === null) {
            return null;
        }

        $sessionId = $this->handleCommand(new CreateSessionCommand(
            $this->getUserIp($request),
            $this->getUserAgent($request),
            $user->getId(),
            time()
        ));
        $this->initSession($request, $sessionId);

        return $user;
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $userForm, Request $request): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert->that($this->captchaValidator->verify($request))->true('register.errors.captcha_invalid');
        $lazyAssert
            ->that($userForm['gender'])
            ->notEmpty('register.errors.gender_missing')
            ->that($userForm['username'])
            ->notEmpty('register.errors.username_missing')
            ->betweenLength(3, 20, 'register.errors.username_length')
            ->that($userForm['first_name'])
            ->notEmpty('register.errors.first_name_missing')
            ->betweenLength(1, 25, 'register.errors.first_name_length')
            ->that($userForm['last_name'])
            ->notEmpty('register.errors.last_name_missing')
            ->betweenLength(1, 25, 'register.errors.last_name_length')
            ->that($userForm['email'])
            ->notEmpty('register.errors.email_missing')
            ->email('register.errors.email_invalid_or_already_exists')
            ->that($userForm['birth_day'])
            ->notEmpty('register.errors.birth_day_missing')
            ->between(1, 31, 'register.errors.birth_day_range')
            ->that($userForm['birth_month'])
            ->notEmpty('register.errors.birth_month_missing')
            ->between(1, 12, 'register.errors.birth_month_range')
            ->that($userForm['birth_year'])
            ->notEmpty('register.errors.birth_year_missing')
            ->between(1900, (int) date('Y'), 'register.errors.birth_year_range')
            ->that($userForm['password'])
            ->notEmpty('register.errors.password_missing')
            ->minLength(6, 'register.errors.password_too_short')
            ->that($userForm['password_repeat'])
            ->eq($userForm['password'], 'register.errors.password_does_not_match')
        ;
        $lazyAssert->verifyNow();

        $userByUsername = $this->userDataProvider->getUserByEmailOrUsername($userForm['username']);
        $lazyAssert->that($userByUsername)->null('register.errors.username_already_exists');

        $userByEmail = $this->userDataProvider->getUserByEmailOrUsername($userForm['email']);
        $lazyAssert->that($userByEmail)->null('register.errors.email_invalid_or_already_exists');

        $lazyAssert->verifyNow();
    }
}
