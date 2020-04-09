<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdateUserCommand extends AbstractCommand
{
    private string $email;
    private ?UploadedFile $avatar;
    private bool $newsletter;

    public function __construct(string $email, ?UploadedFile $avatar, bool $newsletter, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->email = $email;
        $this->avatar = $avatar;
        $this->newsletter = $newsletter;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAvatar(): ?UploadedFile
    {
        return $this->avatar;
    }

    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }
}
