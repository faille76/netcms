<?php

declare(strict_types=1);

namespace App\Partner\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreatePartnerCommand
{
    private string $name;
    private string $url;
    private UploadedFile $image;
    private bool $enabled;
    private int $userId;
    private int $occurredOn;

    public function __construct(string $name, string $url, UploadedFile $image, bool $enabled, int $userId, int $occurredOn)
    {
        $this->name = $name;
        $this->url = $url;
        $this->image = $image;
        $this->enabled = $enabled;
        $this->userId = $userId;
        $this->occurredOn = $occurredOn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getOccurredOn(): int
    {
        return $this->occurredOn;
    }
}
