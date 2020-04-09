<?php

declare(strict_types=1);

namespace App\Contact\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class SendEmailCommand extends AbstractCommand
{
    private string $from;
    private string $subject;
    private string $email;
    private string $content;

    public function __construct(
        string $from,
        string $subject,
        string $email,
        string $content,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->from = $from;
        $this->subject = $subject;
        $this->email = $email;
        $this->content = $content;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
