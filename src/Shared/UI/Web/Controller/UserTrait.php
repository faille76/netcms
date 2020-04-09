<?php

declare(strict_types=1);

namespace App\Shared\UI\Web\Controller;

use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait UserTrait
{
    protected function getUserLoggedIn(): User
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            return $user;
        }

        throw new UnauthorizedHttpException('Not logged in.');
    }

    protected function getUserId(): int
    {
        return $this->getUserLoggedIn()->getId();
    }

    protected function getUserIp(Request $request): string
    {
        return $request->getClientIp() ?? 'null';
    }

    protected function getUserAgent(Request $request): string
    {
        return $request->headers->get('User-Agent', 'null') ?? 'null';
    }

    protected function initSession(Request $request, string $sessionId): void
    {
        $session = new Session();
        $session->set('sessionId', $sessionId);
        $session->start();
        $request->setSession($session);
    }
}
