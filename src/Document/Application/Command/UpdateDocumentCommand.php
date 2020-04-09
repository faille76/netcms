<?php

declare(strict_types=1);

namespace App\Document\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateDocumentCommand extends AbstractCommand
{
    private int $documentId;
    private string $name;

    public function __construct(
        int $documentId,
        string $name,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->documentId = $documentId;
        $this->name = $name;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
