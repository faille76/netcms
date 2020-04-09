<?php

declare(strict_types=1);

namespace App\Page\Application\Event;

final class PageCreatedEvent extends AbstractPageEvent
{
    public function __construct(
        int $pageId,
        string $slug,
        int $parentId,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($pageId, $userId, $occurredOn);
        $this->addProperties([
            'slug' => $slug,
            'parentId' => $parentId,
        ]);
    }

    public function getSlug(): string
    {
        return (string) $this->getProperty('slug', 'null');
    }

    public function getParentId(): int
    {
        return (int) $this->getProperty('parentId', 0);
    }
}
