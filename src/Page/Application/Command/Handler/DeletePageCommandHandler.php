<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\DeletePageCommand;
use App\Page\Application\Command\UpdateParentPageCommand;
use App\Page\Application\Event\PageDeletedEvent;
use App\Page\Domain\Page;
use App\Page\Domain\Persister\ImagePageDataPersisterInterface;
use App\Page\Domain\Persister\PageDataPersisterInterface;
use App\Page\Domain\Persister\PageLangDataPersisterInterface;
use App\Page\Domain\Provider\PageDataProviderInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeletePageCommandHandler implements CommandHandlerInterface
{
    use CommandHandleTrait;

    private PageDataProviderInterface $pageDataProvider;
    private PageDataPersisterInterface $pageDataPersister;
    private PageLangDataPersisterInterface $pageLangDataPersister;
    private ImagePageDataPersisterInterface $imagePageDataPersister;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        PageDataProviderInterface $pageDataProvider,
        PageDataPersisterInterface $pageDataPersister,
        PageLangDataPersisterInterface $pageLangDataPersister,
        ImagePageDataPersisterInterface $imagePageDataPersister,
        MessageBusInterface $commandBus,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->pageDataProvider = $pageDataProvider;
        $this->pageDataPersister = $pageDataPersister;
        $this->pageLangDataPersister = $pageLangDataPersister;
        $this->imagePageDataPersister = $imagePageDataPersister;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(DeletePageCommand $command): void
    {
        $page = $this->pageDataProvider->getPageById($command->getPageId(), 'fr');
        if ($page === null) {
            return;
        }
        $subPages = $this->pageDataProvider->findPagesByParentId($command->getPageId(), 'fr');
        /* @var Page $subPage */
        foreach ($subPages as $subPage) {
            $this->commandBus->dispatch(new UpdateParentPageCommand(
                $subPage->getId(),
                $page->getParentId(),
                $command->getUserId(),
                $command->getOccurredOn()
            ));
        }
        $this->pageDataPersister->deletePage($command->getPageId());
        $this->pageLangDataPersister->deleteByPageId($command->getPageId());
        $this->imagePageDataPersister->deleteByPageId($command->getPageId());
        $this->eventDispatcher->dispatch(new PageDeletedEvent(
            $command->getPageId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
