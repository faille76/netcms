<?php

declare(strict_types=1);

namespace App\Document\Infrastructure\Storage\Mysql;

use App\Document\Domain\Document;
use App\Document\Domain\DocumentCollection;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class DocumentDataProvider extends AbstractDataProvider implements DocumentDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getDocument(int $documentId): ?Document
    {
        $qb = $this->createDocumentQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('document.id = :document_id')
            ->setParameter('document_id', $documentId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createDocumentFromRow($row);
    }

    public function findDocuments(
        ?bool $enabled = null,
        Criteria $criteria = null
    ): DocumentCollection {
        $documentCollection = new DocumentCollection();

        $qb = $this->createDocumentQueryBuilder();
        if ($enabled !== null) {
            $qb
                ->andWhere('enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0, 'integer')
            ;
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $documentCollection->add($this->createDocumentFromRow($row));
        }

        return $documentCollection;
    }

    public function countDocuments(?bool $enabled = null): int
    {
        $qb = $this->createQueryBuilder()
            ->select('count(id)')
            ->from('upload', 'document')
        ;

        if ($enabled !== null) {
            $qb
                ->andWhere('enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0, 'integer')
            ;
        }

        return $this->fetchIntColumn($qb);
    }

    public function findForSearch(array $keys): DocumentCollection
    {
        $documentCollection = new DocumentCollection();

        $qb = $this->createDocumentQueryBuilder()
            ->andWhere('enabled = :enabled')
            ->setParameter('enabled', 1, 'integer')
        ;

        foreach ($keys as $id => $key) {
            $qb
                ->andWhere('document.name LIKE :key' . $id . ' OR document.file_name LIKE :key' . $id)
                ->setParameter('key' . $id, '%' . addcslashes($key, '%_') . '%')
            ;
        }

        $qb->orderBy('document.created_at', 'DESC');

        foreach ($this->fetchAll($qb) as $row) {
            $documentCollection->add($this->createDocumentFromRow($row));
        }

        return $documentCollection;
    }

    private function createDocumentFromRow(array $row): Document
    {
        $row['id'] = (int) $row['id'];
        $row['enabled'] = (bool) $row['enabled'];
        $row['author'] = [
            'user_id' => (int) $row['author_id'],
            'first_name' => $row['author_first_name'],
            'last_name' => $row['author_last_name'],
            'avatar' => $row['author_avatar'],
        ];
        unset($row['author_id'], $row['author_first_name'], $row['author_last_name'], $row['author_avatar']);

        return Document::fromArray($row);
    }

    private function createDocumentQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'document.id',
                'document.name',
                'document.file_name',
                'document.created_at',
                'document.enabled',
                'document.type',
                'author.id as author_id',
                'author.first_name as author_first_name',
                'author.last_name as author_last_name',
                'author.avatar as author_avatar',
            ])
            ->from('upload', 'document')
            ->innerJoin('document', 'users', 'author', 'author.id = document.user_id')
        ;
    }
}
