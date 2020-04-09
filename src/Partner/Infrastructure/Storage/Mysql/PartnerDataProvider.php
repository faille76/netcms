<?php

declare(strict_types=1);

namespace App\Partner\Infrastructure\Storage\Mysql;

use App\Partner\Domain\Partner;
use App\Partner\Domain\PartnerCollection;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class PartnerDataProvider extends AbstractDataProvider implements PartnerDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function findPartners(
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): PartnerCollection {
        $partnerCollection = new PartnerCollection();

        $qb = $this->createPartnerQueryBuilder();
        if ($enabled !== null) {
            $qb
                ->andWhere('enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0)
            ;
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $partnerCollection->add($this->createPartnerFromRow($row));
        }

        return $partnerCollection;
    }

    public function getPartnerById(int $partnerId): ?Partner
    {
        $qb = $this->createPartnerQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('partner.id = :partner_id')
            ->setParameter('partner_id', $partnerId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createPartnerFromRow($row);
    }

    public function countPartners(?bool $enabled = null): int
    {
        $qb = $this->createQueryBuilder()
            ->select('count(*)')
            ->from('partners', 'partner')
        ;

        if ($enabled !== null) {
            $qb
                ->andWhere('enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0)
            ;
        }

        return $this->fetchIntColumn($qb);
    }

    private function createPartnerFromRow(array $row): Partner
    {
        $row['id'] = (int) $row['id'];
        $row['enabled'] = (bool) $row['enabled'];

        return Partner::fromArray($row);
    }

    private function createPartnerQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'partner.id',
                'partner.name',
                'partner.image',
                'partner.url',
                'partner.enabled',
            ])
            ->from('partners', 'partner')
        ;
    }
}
