<?php

declare(strict_types=1);

namespace App\ImageFactory\Application\Command;

use App\Shared\Application\Command\AbstractCommand;

final class ResizeImageCommand extends AbstractCommand
{
    private string $path;
    private string $fileName;
    private int $filterType;
    private string $format;

    public function __construct(string $path, string $fileName, int $filterType, string $format, int $userId, int $occurredOn)
    {
        parent::__construct($userId, $occurredOn);
        $this->path = $path;
        $this->fileName = $fileName;
        $this->filterType = $filterType;
        $this->format = $format;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFilterType(): int
    {
        return $this->filterType;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
