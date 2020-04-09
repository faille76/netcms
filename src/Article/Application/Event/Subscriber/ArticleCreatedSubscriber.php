<?php

declare(strict_types=1);

namespace App\Article\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleCreatedEvent;
use App\Contact\Application\Command\SendManyEmailCommand;
use App\Core\Domain\ConfigRepositoryInterface;
use App\User\Domain\Provider\MailingDataProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class ArticleCreatedSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private MessageBusInterface $commandBus;
    private MailingDataProviderInterface $mailingDataProvider;
    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        Environment $twig,
        MessageBusInterface $commandBus,
        ConfigRepositoryInterface $configRepository,
        MailingDataProviderInterface $mailingDataProvider
    ) {
        $this->twig = $twig;
        $this->commandBus = $commandBus;
        $this->mailingDataProvider = $mailingDataProvider;
        $this->configRepository = $configRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleCreatedEvent::class => 'onArticleCreated',
        ];
    }

    public function onArticleCreated(ArticleCreatedEvent $event): void
    {
        $body = $this->twig->render('mail/article_added.html.twig', [
            'slug' => $event->getSlug(),
        ]);

        $emails = $this->mailingDataProvider->findEmails();
        $this->commandBus->dispatch(new SendManyEmailCommand(
            (string) $this->configRepository->get('email_address'),
            $event->getName(),
            $emails,
            $body,
            0,
            $event->getOccurredOn()
        ));
    }
}
