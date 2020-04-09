<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use App\User\Application\Command\CreateSessionCommand;
use App\User\Domain\Provider\UserDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        UserDataProviderInterface $userDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->userDataProvider = $userDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/login", name="user_login_submit", methods={"POST"})
     */
    public function post(Request $request): Response
    {
        if ($request->request->has('username') && $request->request->has('password')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $user = $this->userDataProvider->getUserByEmailOrUsername($username);
            if ($user === null) {
                $message = 'login.errors.no_account';
            } else {
                if (md5(md5($password)) != $user->getPassword()) {
                    $message = 'login.errors.password_does_not_match';
                } else {
                    $sessionId = $this->handleCommand(new CreateSessionCommand(
                        $this->getUserIp($request),
                        $this->getUserAgent($request),
                        $user->getId(),
                        time()
                    ));
                    $this->initSession($request, $sessionId);

                    return $this->redirectToRoute('home.lang', [
                        '_locale' => $request->getLocale(),
                    ]);
                }
            }
        } else {
            $message = 'login.errors.missing_fields';
        }

        return $this->render('login/index.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/login", name="user_login", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('login/index.html.twig');
    }
}
