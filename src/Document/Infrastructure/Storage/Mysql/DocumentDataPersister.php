<?php

declare(strict_types=1);

namespace App\Document\Infrastructure\Storage\Mysql;

use App\Document\Domain\Persister\DocumentDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class DocumentDataPersister extends AbstractDataPersister implements DocumentDataPersisterInterface
{
    public function createDocument(string $name, string $fileName, int $userId, string $type, bool $enabled): int
    {
        $this->insert('upload', [
            'name' => $name,
            'file_name' => $fileName,
            'user_id' => $userId,
            'type' => $type,
            'enabled' => $enabled ? 1 : 0,
        ]);

        return $this->getLastInsertId();
    }

    public function updateDocument(int $documentId, string $name): void
    {
        $this->update('upload', [
            'name' => $name,
        ], [
            'id' => $documentId,
        ]);
    }

    public function updateDocumentEnabled(int $documentId, bool $enabled): void
    {
        $this->update('upload', [
            'enabled' => $enabled ? 1 : 0,
        ], [
            'id' => $documentId,
        ]);
    }

    public function deleteDocument(int $documentId): void
    {
        $this->delete('upload', [
            'id' => $documentId,
        ]);
    }
}
