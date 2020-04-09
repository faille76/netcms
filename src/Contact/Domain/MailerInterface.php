<?php

declare(strict_types=1);

namespace App\Contact\Domain;

interface MailerInterface
{
    public function send(string $from, string $subject, string $email, string $content): void;

    public function sendMany(string $from, string $subject, array $email, string $content): void;
}
