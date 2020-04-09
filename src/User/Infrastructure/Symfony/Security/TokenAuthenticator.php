<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Symfony\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @inheritdoc
     * @return array<string>
     */
    public function getCredentials(Request $request): array
    {
        $token = null;
        if ($request->hasSession() && $request->getSession()->has('sessionId')) {
            $token = $request->getSession()->get('sessionId');
        }

        return [
            'token' => $token,
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apikey = $credentials['token'];
        if ($apikey === null) {
            return null;
        }

        return $userProvider->getUserFromSession($apikey);
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request): bool
    {
        return $request->getSession()->has('sessionId');
    }
}
