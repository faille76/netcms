<?php

declare(strict_types=1);

namespace App\Page\Application\Command\Handler;

use App\Page\Application\Command\CreatePageLangCommand;
use App\Page\Domain\Persister\PageLangDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;

final class CreatePageLangCommandHandler implements CommandHandlerInterface
{
    private PageLangDataPersisterInterface $pageLangDataPersister;

    public function __construct(PageLangDataPersisterInterface $pageLangDataPersister)
    {
        $this->pageLangDataPersister = $pageLangDataPersister;
    }

    public function __invoke(CreatePageLangCommand $command): int
    {
        return $this->pageLangDataPersister->createPageLang(
            $command->getPageId(),
            $command->getLang(),
            $command->getName(),
            $command->getContent()
        );
    }
}
