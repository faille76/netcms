<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateImgPageCommand extends AbstractCommand
{
    private int $pageId;
    private string $name;
    private UploadedFile $image;

    public function __construct(int $pageId, string $name, UploadedFile $image, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->pageId = $pageId;
        $this->name = $name;
        $this->image = $image;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }
}
