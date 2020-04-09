<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\Storage\Mysql;

use App\Contact\Domain\Contact;
use App\Contact\Domain\ContactCollection;
use App\Contact\Domain\Provider\ContactDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class ContactDataProvider extends AbstractDataProvider implements ContactDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getContact(int $contactId): ?Contact
    {
        $qb = $this->createContactQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('contact.id = :id')
            ->setParameter('id', $contactId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createContactFromRow($row);
    }

    public function findContacts(?Criteria $criteria = null): ContactCollection
    {
        $contactCollection = new ContactCollection();

        $qb = $this->createContactQueryBuilder();

        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $contactCollection->add($this->createContactFromRow($row));
        }

        return $contactCollection;
    }

    private function createContactFromRow(array $row): Contact
    {
        $row['id'] = (int) $row['id'];

        return Contact::fromArray($row);
    }

    private function createContactQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'contact.id',
                'contact.last_name',
                'contact.first_name',
                'contact.email',
                'contact.role as role',
            ])
            ->from('contacts', 'contact')
        ;
    }
}
