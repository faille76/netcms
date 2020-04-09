<?php

declare(strict_types=1);

namespace App\Document\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateDocumentEnabledCommand extends AbstractCommand
{
    private int $documentId;
    private bool $enabled;

    public function __construct(
        int $documentId,
        bool $enabled,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->documentId = $documentId;
        $this->enabled = $enabled;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
