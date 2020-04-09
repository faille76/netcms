<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Command\UpdateUserPasswordCommand;
use App\User\Domain\Provider\UserDataProviderInterface;
use App\User\Domain\User;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;
    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        UserDataProviderInterface $userDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->userDataProvider = $userDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/profile", name="user_profile_submit", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PROFILE_UPDATE)) {
            throw $this->createNotFoundException();
        }

        $userForm = [
            'email' => $request->request->get('email', null),
            'password' => $request->request->get('password', null),
            'password_repeat' => $request->request->get('password_repeat', null),
            'avatar' => $request->files->get('avatar', null),
            'newsletter' => $request->request->get('newsletter', 'off') === 'on' ? true : false,
        ];

        $render = [];

        try {
            $this->validate($userForm, $this->getUserLoggedIn());
            $this->handleCommand(new UpdateUserCommand(
                $userForm['email'],
                $userForm['avatar'],
                $userForm['newsletter'],
                $this->getUserId(),
                time()
            ));
            if (!empty($userForm['password'])) {
                $this->handleCommand(new UpdateUserPasswordCommand(
                    md5(md5($userForm['password'])),
                    $this->getUserId(),
                    time()
                ));
            }

            $render['succeed'] = 'profile.confirm.message';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('profile/index.html.twig', $render);
    }

    /**
     * @Route("/profile", name="user_profile", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(string $message = null): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PROFILE_UPDATE)) {
            throw $this->createNotFoundException();
        }

        return $this->render('profile/index.html.twig');
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $userForm, User $user): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($userForm['email'])
            ->notEmpty('profile.errors.email_invalid')
            ->email('profile.errors.email_invalid')
        ;
        if (!empty($userForm['password']) || !empty($userForm['password_repeat'])) {
            $lazyAssert
                ->that($userForm['password'])
                ->notEmpty('profile.errors.password_too_short')
                ->minLength(6, 'profile.errors.password_too_short')
                ->that($userForm['password_repeat'])
                ->eq($userForm['password'], 'profile.errors.password_does_not_match')
            ;
        }
        if ($userForm['avatar'] !== null) {
            $lazyAssert->that($userForm['avatar']->isValid())->true('profile.errors.avatar_invalid');
            $lazyAssert->that($userForm['avatar']->getMimeType())->inArray(MimeTypeEnum::toArray(), 'profile.errors.avatar_invalid');
        }
        $lazyAssert->verifyNow();

        if ($userForm['email'] !== $user->getEmail()) {
            $userByEmail = $this->userDataProvider->getUserByEmailOrUsername($userForm['email']);
            $lazyAssert->that($userByEmail)->null('profile.errors.email_already');
        }

        $lazyAssert->verifyNow();
    }
}
