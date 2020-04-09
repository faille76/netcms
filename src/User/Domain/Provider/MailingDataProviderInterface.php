<?php

declare(strict_types=1);

namespace App\User\Domain\Provider;

use App\Shared\Domain\Criteria;

interface MailingDataProviderInterface
{
    public function findEmails(?Criteria $criteria = null): array;
}
