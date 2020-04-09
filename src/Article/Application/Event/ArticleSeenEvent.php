<?php

declare(strict_types=1);

namespace App\Article\Application\Event;

use App\Shared\Application\Event\AbstractBaseEvent;

final class ArticleSeenEvent extends AbstractBaseEvent
{
    public function __construct(
        int $articleId,
        string $ip,
        ?int $userId,
        int $occurredOn
    ) {
        parent::__construct($occurredOn);
        $this->addProperties([
            'articleId' => $articleId,
            'ip' => $ip,
            'userId' => $userId,
        ]);
    }

    public function getArticleId(): int
    {
        return (int) $this->getProperty('articleId', 0);
    }

    public function getIp(): string
    {
        return (string) $this->getProperty('ip', 'null');
    }

    public function getUserId(): ?int
    {
        return $this->getProperty('userId', null);
    }
}
