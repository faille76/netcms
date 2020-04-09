<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\Service;

use App\Contact\Domain\MailerInterface;
use App\Core\Domain\ConfigRepositoryInterface;

final class Mailer implements MailerInterface
{
    private \Swift_Mailer $mailer;
    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        \Swift_Mailer $mailer,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->mailer = $mailer;
        $this->configRepository = $configRepository;
    }

    public function send(string $from, string $subject, string $email, string $content): void
    {
        $message = (new \Swift_Message($subject))
            ->setFrom((string) $this->configRepository->get('email_address'))
            ->setReplyTo($from)
            ->setTo($email)
            ->setBody(
                $content,
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function sendMany(string $from, string $subject, array $email, string $content): void
    {
        $message = (new \Swift_Message($subject))
            ->setFrom((string) $this->configRepository->get('email_address'))
            ->setReplyTo($from)
            ->setBcc($email)
            ->setBody(
                $content,
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
