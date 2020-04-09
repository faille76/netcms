<?php

declare(strict_types=1);

namespace App\Partner\Domain\Provider;

use App\Partner\Domain\Partner;
use App\Partner\Domain\PartnerCollection;
use App\Shared\Domain\Criteria;

interface PartnerDataProviderInterface
{
    public function findPartners(
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): PartnerCollection;

    public function getPartnerById(int $partnerId): ?Partner;

    public function countPartners(?bool $enabled = null): int;
}
