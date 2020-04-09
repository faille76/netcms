<?php

declare(strict_types=1);

namespace App\Document\Domain\Provider;

use App\Document\Domain\Document;
use App\Document\Domain\DocumentCollection;
use App\Shared\Domain\Criteria;

interface DocumentDataProviderInterface
{
    public function getDocument(int $documentId): ?Document;

    public function findDocuments(
        ?bool $enabled = null,
        Criteria $criteria = null
    ): DocumentCollection;

    public function countDocuments(?bool $enabled = null): int;

    public function findForSearch(
        array $keys
    ): DocumentCollection;
}
