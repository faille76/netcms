<?php

declare(strict_types=1);

namespace App\Article\Application\Event;

final class ArticleCreatedEvent extends AbstractArticleEvent
{
    public function __construct(
        int $articleId,
        string $name,
        ?string $image,
        string $slug,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($articleId, $userId, $occurredOn);
        $this->addProperties([
            'name' => $name,
            'image' => $image,
            'slug' => $slug,
        ]);
    }

    public function getName(): string
    {
        return (string) $this->getProperty('name', '');
    }

    public function getImage(): ?string
    {
        return $this->getProperty('image', null);
    }

    public function getSlug(): string
    {
        return (string) $this->getProperty('slug', '');
    }
}
