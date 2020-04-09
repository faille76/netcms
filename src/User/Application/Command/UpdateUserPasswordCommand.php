<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class UpdateUserPasswordCommand extends AbstractCommand
{
    private string $password;

    public function __construct(string $password, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
