<?php

declare(strict_types=1);

namespace App\Contact\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class DeleteContactCommand extends AbstractCommand
{
    private int $contactId;

    public function __construct(
        int $contactId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->contactId = $contactId;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }
}
