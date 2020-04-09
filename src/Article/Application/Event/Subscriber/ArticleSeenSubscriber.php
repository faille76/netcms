<?php

declare(strict_types=1);

namespace App\Article\Application\Event\Subscriber;

use App\Article\Application\Event\ArticleSeenEvent;
use App\Article\Domain\Persister\ArticleDataPersisterInterface;
use App\ViewCache\Domain\ViewerCacheDataPersisterInterface;
use App\ViewCache\Domain\ViewerCacheDataProviderInterface;
use App\ViewCache\Domain\ViewerTypeEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ArticleSeenSubscriber implements EventSubscriberInterface
{
    private ArticleDataPersisterInterface $articleDataPersister;
    private ViewerCacheDataProviderInterface $viewerCacheDataProvider;
    private ViewerCacheDataPersisterInterface $viewerCacheDataPersister;

    public function __construct(
        ArticleDataPersisterInterface $articleDataPersister,
        ViewerCacheDataProviderInterface $viewerCacheDataProvider,
        ViewerCacheDataPersisterInterface $viewerCacheDataPersister
    ) {
        $this->articleDataPersister = $articleDataPersister;
        $this->viewerCacheDataProvider = $viewerCacheDataProvider;
        $this->viewerCacheDataPersister = $viewerCacheDataPersister;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleSeenEvent::class => 'onArticleSeen',
        ];
    }

    public function onArticleSeen(ArticleSeenEvent $event): void
    {
        if (!$this->viewerCacheDataProvider->hasViewed($event->getArticleId(), ViewerTypeEnum::ARTICLE, $event->getIp())) {
            $this->viewerCacheDataPersister->addViewer($event->getArticleId(), ViewerTypeEnum::ARTICLE, $event->getIp());
            $this->articleDataPersister->increaseArticleView($event->getArticleId());
        }

        if (
            $event->getUserId() !== null
            &&
            !$this->viewerCacheDataProvider->hasUserViewed($event->getArticleId(), ViewerTypeEnum::ARTICLE, $event->getUserId())
        ) {
            $this->viewerCacheDataPersister->addUserViewed($event->getArticleId(), ViewerTypeEnum::ARTICLE, $event->getUserId());
        }
    }
}
