<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Storage\Mysql;

use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use App\User\Domain\Provider\UserDataProviderInterface;
use App\User\Domain\Rights;
use App\User\Domain\User;
use App\User\Domain\UserCollection;
use Doctrine\DBAL\Query\QueryBuilder;

final class UserDataProvider extends AbstractDataProvider implements UserDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getUserById(int $userId): ?User
    {
        $qb = $this->createUserQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('user.id = :user_id')
            ->setParameter('user_id', $userId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    public function getUserBySessionId(string $sessionId): ?User
    {
        $qb = $this->createUserQueryBuilder()
            ->setMaxResults(1)
            ->addSelect('session.token as session')
            ->rightJoin('user', 'sessions', 'session', 'session.user_id = user.id')
            ->andWhere('session.token = :session_id')
            ->setParameter('session_id', $sessionId, 'string')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    public function getUserByEmailOrUsername(string $value): ?User
    {
        $qb = $this->createUserQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('user.username = :value')
            ->orWhere('user.email = :value')
            ->setParameter('value', $value, 'string')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    public function findUsers(?Criteria $criteria = null): UserCollection
    {
        $userCollection = new UserCollection();

        $qb = $this->createUserQueryBuilder();
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $userCollection->add($this->createUserFromRow($row));
        }

        return $userCollection;
    }

    public function countUsers(): int
    {
        $qb = $this->createQueryBuilder()
            ->select('count(id)')
            ->from('users', 'user')
        ;

        return $this->fetchIntColumn($qb);
    }

    private function createUserFromRow(array $row): User
    {
        $row['id'] = (int) $row['id'];
        $row['newsletter_enabled'] = (bool) $row['newsletter_enabled'];
        if ($row['rights'] !== null) {
            $row['rights'] = Rights::fromArray(json_decode($row['rights'], true));
        } else {
            $row['rights'] = new Rights();
        }

        return User::fromArray($row);
    }

    private function createUserQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'user.id',
                'user.username',
                'user.password',
                'user.last_name',
                'user.first_name',
                'user.email',
                'user.created_at',
                'user.updated_at',
                'user.birthday',
                'user.gender',
                'user.avatar',
                'user.newsletter as newsletter_enabled',
                'user.rights',
            ])
            ->from('users', 'user')
        ;
    }
}
