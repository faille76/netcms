<?php

declare(strict_types=1);

namespace App\Document\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteDocumentCommand extends AbstractCommand
{
    private int $documentId;

    public function __construct(
        int $documentId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->documentId = $documentId;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getOccurredOn(): int
    {
        return $this->occurredOn;
    }
}
