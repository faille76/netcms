<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Symfony\Security;

use App\User\Domain\Provider\UserDataProviderInterface;
use App\User\Domain\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ApiKeyUserProvider implements UserProviderInterface
{
    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        UserDataProviderInterface $userDataProvider
    ) {
        $this->userDataProvider = $userDataProvider;
    }

    public function getUserFromSession(string $sessionId): ?User
    {
        $user = $this->userDataProvider->getUserBySessionId($sessionId);
        if ($user === null) {
            return null;
        }

        return $user;
    }

    public function getUserByUsername(string $username): User
    {
        $user = $this->userDataProvider->getUserByEmailOrUsername($username);
        if ($user === null) {
            throw new \InvalidArgumentException('No user found for user ' . $username);
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function loadUserByUsername(string $username)
    {
        return $this->getUserByUsername($username);
    }

    /**
     * @inheritdoc
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    /**
     * @inheritdoc
     */
    public function supportsClass(string $class)
    {
        return $class === User::class;
    }
}
