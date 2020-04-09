<?php

declare(strict_types=1);

namespace App\Document\Domain\Persister;

interface DocumentDataPersisterInterface
{
    public function createDocument(string $name, string $fileName, int $userId, string $type, bool $enabled): int;

    public function updateDocument(int $documentId, string $name): void;

    public function updateDocumentEnabled(int $documentId, bool $enabled): void;

    public function deleteDocument(int $documentId): void;
}
