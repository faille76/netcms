<?php

declare(strict_types=1);

namespace App\Article\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdateArticleCommand extends AbstractCommand
{
    private int $articleId;
    private string $name;
    private ?UploadedFile $image;
    private string $text;

    public function __construct(
        int $articleId,
        string $name,
        ?UploadedFile $image,
        string $text,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->articleId = $articleId;
        $this->name = $name;
        $this->image = $image;
        $this->text = $text;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
