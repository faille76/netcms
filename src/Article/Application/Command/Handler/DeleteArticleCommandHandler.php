<?php

declare(strict_types=1);

namespace App\Article\Application\Command\Handler;

use App\Article\Application\Command\DeleteArticleCommand;
use App\Article\Application\Event\ArticleDeletedEvent;
use App\Article\Domain\ArticleImage;
use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteArticleCommandHandler implements CommandHandlerInterface
{
    private ArticleDataProviderInterface $articleDataProvider;
    private ArticleDataPersisterInterface $articleDataPersister;
    private EventDispatcherInterface $eventDispatcher;
    private PhysicalDataPersisterInterface $physicalDataPersister;

    public function __construct(
        ArticleDataProviderInterface $articleDataProvider,
        ArticleDataPersisterInterface $articleDataPersister,
        PhysicalDataPersisterInterface $physicalDataPersister,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->articleDataProvider = $articleDataProvider;
        $this->articleDataPersister = $articleDataPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->physicalDataPersister = $physicalDataPersister;
    }

    public function __invoke(DeleteArticleCommand $command): void
    {
        $article = $this->articleDataProvider->getArticleById($command->getArticleId());
        if ($article === null) {
            throw new NotFoundException('Article ' . $command->getArticleId() . ' does not exists.');
        }
        if ($article->getImage() !== null) {
            $this->physicalDataPersister->remove(ArticleImage::PATH_BASE . $article->getImage());
            foreach (ArticleImage::FILTERS as $filterType) {
                $this->physicalDataPersister->remove(
                    ArticleImage::PATH_BASE . FilterTypeEnum::getFilterName($filterType) . '/' . $article->getImage()
                );
            }
        }

        $this->articleDataPersister->deleteArticle($command->getArticleId());

        $this->eventDispatcher->dispatch(new ArticleDeletedEvent(
            $command->getArticleId(),
            $command->getUserId(),
            $command->getOccurredOn()
        ));
    }
}
