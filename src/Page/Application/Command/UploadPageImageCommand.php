<?php

declare(strict_types=1);

namespace App\Page\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadPageImageCommand extends AbstractCommand
{
    private UploadedFile $file;
    private string $slug;
    private string $pageSlug;

    public function __construct(UploadedFile $file, string $pageSlug, string $slug, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->file = $file;
        $this->pageSlug = $pageSlug;
        $this->slug = $slug;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPageSlug(): string
    {
        return $this->pageSlug;
    }
}
