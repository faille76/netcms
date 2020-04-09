<?php

declare(strict_types=1);

namespace App\Contact\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class SendManyEmailCommand extends AbstractCommand
{
    private string $from;
    private string $subject;
    private array $emails;
    private string $content;

    public function __construct(
        string $from,
        string $subject,
        array $emails,
        string $content,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->from = $from;
        $this->subject = $subject;
        $this->emails = $emails;
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

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
