<?php

declare(strict_types=1);

namespace App\Partner\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadPartnerImageCommand extends AbstractCommand
{
    private UploadedFile $file;
    private string $slug;

    public function __construct(UploadedFile $file, string $slug, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->file = $file;
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
}
