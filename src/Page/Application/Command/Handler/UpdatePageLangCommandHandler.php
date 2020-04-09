<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\UpdatePageLangCommand;
use App\Page\Application\Event\PageContentUpdatedEvent;
use App\Page\Domain\Persister\PageLangDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdatePageLangCommandHandler implements CommandHandlerInterface
{
    private PageLangDataPersisterInterface $pageLangDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PageLangDataPersisterInterface $pageLangDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->pageLangDataPersister = $pageLangDataPersister;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdatePageLangCommand $command): void
    {
        $this->pageLangDataPersister->updatePageLang(
            $command->getPageId(),
            $command->getLang(),
            $command->getName(),
            $command->getContent()
        );
        $this->eventDispatcher->dispatch(new PageContentUpdatedEvent(
            $command->getPageId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
