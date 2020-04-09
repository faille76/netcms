<?php

declare(strict_types=1);

namespace App\Document\Application\Command;

use App\Shared\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateDocumentCommand extends AbstractCommand
{
    private string $name;
    private UploadedFile $file;
    private bool $enabled;

    public function __construct(
        string $name,
        UploadedFile $file,
        bool $enabled,
        int $userId,
        int $occurredOn
    ) {
        parent::__construct($userId, $occurredOn);
        $this->name = $name;
        $this->file = $file;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
