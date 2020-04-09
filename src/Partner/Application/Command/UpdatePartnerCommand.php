<?php

declare(strict_types=1);

namespace App\Partner\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UpdatePartnerCommand
{
    private int $partnerId;
    private string $name;
    private string $url;
    private ?UploadedFile $image;
    private int $userId;
    private int $occurredOn;

    public function __construct(int $partnerId, string $name, string $url, ?UploadedFile $image, int $userId, int $occurredOn)
    {
        $this->partnerId = $partnerId;
        $this->name = $name;
        $this->url = $url;
        $this->image = $image;
        $this->userId = $userId;
        $this->occurredOn = $occurredOn;
    }

    public function getPartnerId(): int
    {
        return $this->partnerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
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
