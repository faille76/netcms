<?php

declare(strict_types=1);

namespace App\Contact\Application\Command\Handler;

use App\Contact\Application\Command\SendManyEmailCommand;
use App\Contact\Domain\MailerInterface;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Twig\Environment;

final class SendManyEmailCommandHandler implements CommandHandlerInterface
{
    private PartnerDataProviderInterface $partnerDataProvider;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(
        Environment $twig,
        PartnerDataProviderInterface $partnerDataProvider,
        MailerInterface $mailer
    ) {
        $this->partnerDataProvider = $partnerDataProvider;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(SendManyEmailCommand $command): void
    {
        $body = $this->twig->render('mail/core.html.twig', [
            'body' => $command->getContent(),
            'partners' => $this->partnerDataProvider->findPartners(true),
        ]);
        $this->mailer->sendMany(
            $command->getFrom(),
            $command->getSubject(),
            $command->getEmails(),
            $body
        );
    }
}
