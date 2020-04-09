<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Storage\Mysql;

use App\Shared\Domain\Criteria;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use App\User\Domain\Provider\MailingDataProviderInterface;

final class MailingDataProvider extends AbstractDataProvider implements MailingDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function findEmails(?Criteria $criteria = null): array
    {
        $qb = $this->createQueryBuilder()
            ->from('users', 'user')
            ->select('user.email')
            ->where('user.newsletter = 1')
        ;
        $this->applyCriteria($qb, $criteria);

        $emails = [];
        foreach ($this->fetchAll($qb) as $user) {
            $emails[] = $user['email'];
        }

        return $emails;
    }
}
